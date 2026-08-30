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

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): mixed{
        /// @todo implement admin middleware
        return $next($request);
    }
}