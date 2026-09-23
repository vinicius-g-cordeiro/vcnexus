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
            ],
        ], JSON_THROW_ON_ERROR));
    }

    public function onMessage(
        ConnectionInterface $connection,
        $message
    ): void {
        echo "Client {$connection->resourceId}: {$message}\n";

        $payload = json_decode(
            $message,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $content = trim((string) ($payload['message'] ?? ''));

        if ($content === '') {
            return;
        }

        foreach ($this->clients as $client) {

            $client->send(json_encode([
                'event' => 'message',
                'data' => [
                    'from' => $connection->resourceId,
                    'message' => $content,
                ],
            ], JSON_THROW_ON_ERROR));
        }
    }

    public function onClose(ConnectionInterface $connection): void
    {
        $this->clients->detach($connection);

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
}