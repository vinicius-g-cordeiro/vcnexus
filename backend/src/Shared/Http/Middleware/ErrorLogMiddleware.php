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


use App\Shared\Exceptions\AppException;
use App\Shared\Http\{Request, Response};
use App\Shared\Http\Interfaces\MiddlewareInterface;

class ErrorLogMiddleware implements MiddlewareInterface {
    public function handle(Request $request,?callable $next = null, ...$arguments) : ?Response {
        try {
            return $next($request);
        } catch (AppException $e) {
            Response::log(file: 'error', message: $e->getMessage() . "\r\n" . $e->getTraceAsString() . "\r\n" . json_encode($e, JSON_PRETTY_PRINT), status: $e->getCode(), success: false);
            return Response::json(data: [], message: $e->getMessage())->send($e->getCode(), [], true)->send($e->getCode(), [], true);
        }
    }
}