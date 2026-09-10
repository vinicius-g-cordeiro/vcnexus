<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Shared\Interfaces\MiddlewareInterface;
use App\Shared\Request;
use App\Shared\Connection;
use App\Shared\Session;

final class DatabaseContextMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): mixed
    {
        $session = Session::getInstance();
        $connection = Connection::getInstance();
        $db = $connection->getConnection();

        $user = $session->get('user');

        $db->Execute(
            "SELECT
                set_config('app.tenant_id', ?, false),
                set_config('app.user_id', ?, false)",
            [
                (string) $user->tenant_id,
                (string) $user->id,
            ]
        );

        $db = $connection->getConnection();

        try {
            return $next($request);
        } finally {
            $db->Execute(
                "SELECT
                    set_config('app.tenant_id', '', false),
                    set_config('app.user_id', '', false)"
            );
        }
    }
}
