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
use Ratchet\Http\{HttpServer, OriginCheck};
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;


final class WebSocketApplication
{

    public function __construct()
    {
        error_reporting(
            E_ALL
            ^ E_USER_ERROR
            ^ E_USER_WARNING
            ^ E_USER_NOTICE
            ^ E_DEPRECATED
            ^ E_WARNING
        );

        // The HTTP app sets this in Application; without it the WS process would stamp messages in UTC.
        date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'UTC');
    }


    public function run(): void
    {
        $port = (int) (getenv('WS_PORT') ?: 8080);

        // Hostnames only (Ratchet ignores the port). Override with WS_ALLOWED_ORIGINS="a.com,b.com".
        $allowedOrigins = array_values(array_filter(array_map(
            'trim',
            explode(',', getenv('WS_ALLOWED_ORIGINS') ?: 'localhost,127.0.0.1,vcnexus.local')
        )));

        $server = IoServer::factory(
            new HttpServer(
                new OriginCheck(
                    new WsServer(new ChatHandler()),
                    $allowedOrigins
                )
            ),
            $port,
            '0.0.0.0'
        );

        echo "WebSocket server listening on 0.0.0.0:{$port}\n";

        $server->run();
    }

}