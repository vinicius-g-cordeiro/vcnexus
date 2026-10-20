<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Http\Middleware;

use App\Shared\Http\Request;
use App\Shared\Http\Response;
use App\Shared\Http\Interfaces\MiddlewareInterface;
use App\Shared\Http\Interfaces\RateLimiterInterface;


final class RateLimitMiddleware implements MiddlewareInterface {
    function __construct(private readonly RateLimiterInterface $limiter, private readonly int $maxAttempts, private readonly int $decaySeconds) {}
     

    function handle(?Request $request, ?callable $next = null, ...$arguments) : ?Response {
        $key = $request->ip() . '|' . $request->method . '|' . $request->path;

        if($this->limiter->tooManyAttempts($key, $this->maxAttempts)) {
            Response::json(message: '429 - Too many requests')->send( headers: ['Retry-After' => $this->limiter->retriesAfter($key), 'X-RateLimit-Limit' => $this->maxAttempts, 'X-RateLimit-Remaining' => $this->limiter->attempts($key)], code: 429, bExit: true);
        }

        $count = $this->limiter->hit($key, $this->decaySeconds);

        header('X-RateLimit-Limit: ' . $this->maxAttempts);
        header('X-RateLimit-Remaining: ' . max(0, $this->maxAttempts - $count));
        return $next($request);
    }
}

