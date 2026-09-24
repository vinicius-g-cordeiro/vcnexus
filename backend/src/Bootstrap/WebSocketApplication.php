<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Bootstrap;


use App\Realtime\Handlers\ChatHandler;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

final class WebSocketApplication
{
    public function __construct()
    {

        error_reporting(
            E_ALL
            & ~E_DEPRECATED
            & ~E_USER_DEPRECATED
        );


    }

    public function run(): void
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ChatHandler()
                )
            ),
            8080
        );

        $server->run();
    }

}