<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Realtime\Handlers;

use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Redis\RedisConnectionFactory;
use App\Modules\Chat\Exceptions\ChatException;
use App\Modules\Chat\Repositories\ChatRepository;
use App\Modules\Chat\Services\ChatService;
use Ratchet\{ConnectionInterface, MessageComponentInterface};
use SplObjectStorage;

/**
 * Flow:
 *  1. Browser calls POST /v1/chat/ws-ticket (session cookie) and gets a single-use ticket.
 *  2. Browser opens ws://host:8080?ticket=...  -> onOpen() consumes the ticket from Redis
 *     and remembers {user_id, tenant_id, roles} for that connection.
 *  3. join_room / send_message use ONLY that server-side identity, never ids from the payload.
 */
final class ChatHandler implements MessageComponentInterface
{
    /**
     * Authenticated connections. Storage data = ['user_id' => string, 'tenant_id' => string, 'roles' => ?array]
     */
    private SplObjectStorage $clients;

    /**
     * @var array<int, SplObjectStorage>
     */
    private array $rooms = [];

    private ?\ADOConnection $db = null;
    private ?\Redis $redis = null;

    public function __construct(
        private readonly ConnectionFactory $dbFactory = new ConnectionFactory(),
        private readonly RedisConnectionFactory $redisFactory = new RedisConnectionFactory(),
    ) {
        $this->clients = new SplObjectStorage();
    }

    /*
    |--------------------------------------------------------------------------
    | Ratchet callbacks
    |--------------------------------------------------------------------------
    */

    public function onOpen(ConnectionInterface $connection): void
    {
        parse_str($connection->httpRequest->getUri()->getQuery(), $query);
        $context = $this->consumeTicket((string) ($query['ticket'] ?? ''));

        if ($context === null) {
            $this->sendError($connection, 'Unauthorized.');
            $connection->close();

            return;
        }

        $this->clients->attach($connection, $context);

        echo "Client {$connection->resourceId} connected (user {$context['user_id']})\n";

        $this->send($connection, 'connected', [
            'connection_id' => $connection->resourceId,
            'user_id' => (int) $context['user_id'],
            'message' => 'You are connected',
        ]);
    }

    public function onMessage(ConnectionInterface $connection, $message): void
    {
        if (!$this->clients->contains($connection)) {
            $this->sendError($connection, 'Unauthorized.');
            $connection->close();

            return;
        }

        try {
            $payload = json_decode((string) $message, true, 512, JSON_THROW_ON_ERROR);

            match ($payload['action'] ?? null) {
                'join_room' => $this->joinRoom($connection, (int) ($payload['room_id'] ?? 0)),
                'leave_room' => $this->leaveRoom($connection, (int) ($payload['room_id'] ?? 0)),
                'send_message' => $this->sendMessage(
                    $connection,
                    (int) ($payload['room_id'] ?? 0),
                    trim((string) ($payload['message'] ?? ''))
                ),
                default => $this->sendError($connection, 'Unknown action.'),
            };
        } catch (ChatException $exception) {
            // Expected, user-facing errors (no access, invalid input...).
            $this->sendError($connection, $exception->getMessage());
        } catch (\JsonException) {
            $this->sendError($connection, 'Invalid payload.');
        } catch (\Throwable $exception) {
            // Unexpected: log the real reason, tell the client nothing sensitive,
            // and drop the DB handle so the next message reconnects (covers dropped connections).
            error_log(sprintf('[ws] %s in %s:%d', $exception->getMessage(), $exception->getFile(), $exception->getLine()));
            $this->db = null;
            $this->sendError($connection, 'Internal error.');
        }
    }

    public function onClose(ConnectionInterface $connection): void
    {
        foreach ($this->rooms as $roomId => $members) {
            if ($members->contains($connection)) {
                $members->detach($connection);

                if ($members->count() === 0) {
                    unset($this->rooms[$roomId]);
                }
            }
        }

        if ($this->clients->contains($connection)) {
            $this->clients->detach($connection);
        }

        echo "Client {$connection->resourceId} disconnected\n";
    }

    public function onError(ConnectionInterface $connection, \Exception $exception): void
    {
        echo "Client {$connection->resourceId} error: " . $exception->getMessage() . PHP_EOL;

        $connection->close();
    }

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    */

    private function joinRoom(ConnectionInterface $connection, int $roomId): void
    {
        if ($roomId <= 0) {
            $this->sendError($connection, 'Invalid room ID.');

            return;
        }

        $userId = (int) $this->clients[$connection]['user_id'];

        if (!$this->service($connection)->doesUserHasAccessToRoom($userId, $roomId)) {
            $this->sendError($connection, 'You do not have access to this room.');

            return;
        }

        $this->rooms[$roomId] ??= new SplObjectStorage();
        $this->rooms[$roomId]->attach($connection);

        $this->send($connection, 'room_joined', ['room_id' => $roomId]);

        echo "Client {$connection->resourceId} (user {$userId}) joined room {$roomId}\n";
    }

    private function leaveRoom(ConnectionInterface $connection, int $roomId): void
    {
        if (isset($this->rooms[$roomId]) && $this->rooms[$roomId]->contains($connection)) {
            $this->rooms[$roomId]->detach($connection);

            if ($this->rooms[$roomId]->count() === 0) {
                unset($this->rooms[$roomId]);
            }
        }
    }

    private function sendMessage(ConnectionInterface $connection, int $roomId, string $message): void
    {
        if ($roomId <= 0 || $message === '') {
            $this->sendError($connection, 'Invalid room or message.');

            return;
        }

        if (!isset($this->rooms[$roomId]) || !$this->rooms[$roomId]->contains($connection)) {
            $this->sendError($connection, 'You are not connected to this room.');

            return;
        }

        $userId = (int) $this->clients[$connection]['user_id'];

        // Persist first; only broadcast what was actually stored.
        $saved = $this->service($connection)->saveMessage($roomId, $userId, $message);

        $response = json_encode([
            'event' => 'message',
            'data' => $saved, // id, uuid, room_id, user_id, content, created_at
        ], JSON_THROW_ON_ERROR);

        foreach ($this->rooms[$roomId] as $client) {
            $client->send($response);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Infrastructure
    |--------------------------------------------------------------------------
    */

    /**
     * Builds a ChatService scoped to the connection's user/tenant (RLS context is applied per query).
     */
    private function service(ConnectionInterface $connection): ChatService
    {
        $context = $this->clients[$connection];
        $db = $this->db();

        $repository = new ChatRepository($db, $context['tenant_id'], $context['user_id'], $context['roles']);

        return new ChatService($db, $context['tenant_id'], $context['user_id'], $context['roles'], $repository);
    }

    private function db(): \ADOConnection
    {
        if ($this->db === null || !$this->db->IsConnected()) {
            $this->db = $this->dbFactory->create();
        }

        return $this->db;
    }

    /**
     * Atomically reads and deletes the ticket so it can only be used once.
     *
     * @return array{user_id:string, tenant_id:string, roles:?array}|null
     */
    private function consumeTicket(string $ticket): ?array
    {
        if (!preg_match('/^[a-f0-9]{48}$/', $ticket)) {
            return null;
        }

        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                $this->redis ??= $this->redisFactory->create();
                $raw = $this->redis->getDel("ws_ticket:{$ticket}");

                break;
            } catch (\Throwable $exception) {
                // Stale Redis connection: reset and retry once.
                error_log('[ws] redis error: ' . $exception->getMessage());
                $this->redis = null;
                $raw = false;
            }
        }

        if (!is_string($raw)) {
            return null;
        }

        $data = json_decode($raw, true);

        if (!is_array($data) || empty($data['user_id']) || empty($data['tenant_id'])) {
            return null;
        }

        return [
            'user_id' => (string) $data['user_id'],
            'tenant_id' => (string) $data['tenant_id'],
            'roles' => is_array($data['roles'] ?? null) ? $data['roles'] : null,
        ];
    }

    private function send(ConnectionInterface $connection, string $event, array $data): void
    {
        $connection->send(json_encode(['event' => $event, 'data' => $data], JSON_THROW_ON_ERROR));
    }

    private function sendError(ConnectionInterface $connection, string $message): void
    {
        $this->send($connection, 'error', ['message' => $message]);
    }
}