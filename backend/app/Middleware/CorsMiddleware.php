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
use App\Shared\Response;

class CorsMiddleware implements MiddlewareInterface {

    public function handle(Request $request, callable $next) : Response {
        header('Access-Control-Allow-Origin: ' . getenv('APP_HOST') . ':5173');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, QUERY');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        $response = $next($request);
        return $response;
    }
}