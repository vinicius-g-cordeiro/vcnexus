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
final class LoggingMiddleware implements MiddlewareInterface {

    public function handle(Request $request, callable $next) : mixed {
        $start = microtime(true);

        $result = $next($request);

        // if($result instanceof Response == false) {
        //     Response::json(message: '', status: false, code: 200, bShouldExit: true);
        // }

        $durationMs = round((microtime(true) - $start) * 1000,2);
        error_log(sprintf('[%s] %s %s (%s ms)',date('c'), $request->method, $request->path,$durationMs));
        // Response::log(file: 'info', message: sprintf('[%s] %s %s (%s ms)',date('c'), $request->method, $request->path,$durationMs));

        return $result;
    }
}