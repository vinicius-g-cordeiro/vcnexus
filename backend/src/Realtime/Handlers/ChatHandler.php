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


use Ratchet\{ConnectionInterface, MessageComponentInterface};
use SplObjectStorage;

final class ChatHandler implements MessageComponentInterface
{
    private SplObjectStorage $clients;
    /**
     * @var array<int, SplObjectStorage>
     */
    private array $rooms = [];

    public function __construct()
    {
        $this->clients = new SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $connection): void
    {
        $this->clients->attach($connection);

        echo "Client {$connection->resourceId} connected\n";

        $connection->send(json_encode([
            'event' => 'connected',
            'data' => [
                'connection_id' => $connection->resourceId,
                'message' => 'You are connected'
            ],
        ], JSON_THROW_ON_ERROR));
    }

    public function onMessage(
        ConnectionInterface $connection,
        $message
    ): void {
        try {
            $payload = json_decode(
                $message,
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $action = $payload['action'] ?? null;

            match ($action) {
                'join_room' => $this->joinRoom(
                    $connection,
                    (int) ($payload['room_id'] ?? 0)
                ),

                'leave_room' => $this->leaveRoom(
                    $connection,
                    (int) ($payload['room_id'] ?? 0)
                ),

                'send_message' => $this->sendMessage(
                    $connection,
                    (int) ($payload['room_id'] ?? 0),
                    trim((string) ($payload['message'] ?? ''))
                ),

                default => $this->sendError(
                    $connection,
                    'Unknown action.'
                ),
            };
        } catch (\Throwable $exception) {
            $this->sendError(
                $connection,
                $exception->getMessage()
            );
        }
    }

    private function joinRoom(
        ConnectionInterface $connection,
        int $roomId
    ): void {
        if ($roomId <= 0) {
            $this->sendError($connection, 'Invalid room ID.');

            return;
        }
        /// @TODO Check if the user can access room, etc..

        if (!isset($this->rooms[$roomId])) {
            $this->rooms[$roomId] = new SplObjectStorage();
        }

        $this->rooms[$roomId]->attach($connection);

        $connection->send(json_encode([
            'event' => 'room_joined',
            'data' => [
                'room_id' => $roomId,
            ],
        ], JSON_THROW_ON_ERROR));

        echo "Client {$connection->resourceId} joined room {$roomId}\n";
    }

    private function leaveRoom(
        ConnectionInterface $connection,
        int $roomId
    ): void {
        if (
            isset($this->rooms[$roomId]) &&
            $this->rooms[$roomId]->contains($connection)
        ) {
            $this->rooms[$roomId]->detach($connection);

            if ($this->rooms[$roomId]->count() === 0) {
                unset($this->rooms[$roomId]);
            }
        }
    }

    private function sendMessage(
        ConnectionInterface $connection,
        int $roomId,
        string $message
    ): void {
        if ($roomId <= 0 || $message === '') {
            $this->sendError(
                $connection,
                'Invalid room or message.'
            );

            return;
        }

        if (
            !isset($this->rooms[$roomId]) ||
            !$this->rooms[$roomId]->contains($connection)
        ) {
            $this->sendError(
                $connection,
                'You are not connected to this room.'
            );

            return;
        }

        // Persistence comes here.

        $response = json_encode([
            'event' => 'message',
            'data' => [
                'room_id' => $roomId,
                'message' => $message,
            ],
        ], JSON_THROW_ON_ERROR);

        foreach ($this->rooms[$roomId] as $client) {
            $client->send($response);
        }
    }

    public function onClose(ConnectionInterface $connection): void
    {
        foreach ($this->rooms as $roomId => $clients) {
            if ($clients->contains($connection)) {
                $clients->detach($connection);

                if ($clients->count() === 0) {
                    unset($this->rooms[$roomId]);
                }
            }
        }

        echo "Client {$connection->resourceId} disconnected\n";
    }

    public function onError(
        ConnectionInterface $connection,
        \Exception $exception
    ): void {
        echo "Client {$connection->resourceId} error: "
            . $exception->getMessage()
            . PHP_EOL;

        $this->clients->detach($connection);

        $connection->close();
    }


    private function sendError(
        ConnectionInterface $connection,
        string $message
    ): void {
        $connection->send(json_encode([
            'event' => 'error',
            'data' => [
                'message' => $message,
            ],
        ], JSON_THROW_ON_ERROR));
    }
}