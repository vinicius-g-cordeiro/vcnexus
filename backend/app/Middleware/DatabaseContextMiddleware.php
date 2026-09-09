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
use App\Shared\Context\AuthContext;

final class DatabaseContextMiddleware  implements MiddlewareInterface {
    
    public function handle(Request $request, callable $next) : mixed
    {
        $connection = Connection::getInstance();
        $response = $connection->getConnection()->Execute(
            "SELECT set_config('app.tenant_id', ?, false)",
            [AuthContext::tenantId()]
        );
     
        try {
            return $next($request);
        } finally {
            $connection->getConnection()->Execute("SELECT set_config('app.tenant_id', '', false)");
            AuthContext::clear();
        }
    }
}
