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

use App\Shared\Request;
use App\Shared\Response;

class CorsMiddleware {

    public static function handle(Request $request, callable $next) : Response {
        $response = $next($request);
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, QUERY');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        return $response;
    }
}