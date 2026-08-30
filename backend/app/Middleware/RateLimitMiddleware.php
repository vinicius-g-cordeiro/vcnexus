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
use App\Shared\RateLimiting\RateLimiterInterface;

final class RateLimitMiddleware implements MiddlewareInterface {
    function __construct(private readonly RateLimiterInterface $limiter, private readonly int $maxAttempts, private readonly int $decaySeconds) {}
     

    function handle(?Request $request, callable $next) : mixed {
        $key = $request->ip() . '|' . $request->method . '|' . $request->path;

        if($this->limiter->tooManyAttempts($key, $this->maxAttempts)) {
            Response::json(message: '429 - Too many requests', status: false, code: 429, bShouldExit: true, headers: ['Retry-After' => $this->limiter->retriesAfter($key), 'X-RateLimit-Limit' => $this->maxAttempts, 'X-RateLimit-Remaining' => $this->limiter->attempts($key)]);
        }

        $count = $this->limiter->hit($key, $this->decaySeconds);

        header('X-RateLimit-Limit: ' . $this->maxAttempts);
        header('X-RateLimit-Remaining: ' . max(0, $this->maxAttempts - $count));
        return $next($request);
    }
}

