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

use App\Shared\Domain\Exceptions\ValidationException;
use App\Shared\Http\Interfaces\MiddlewareInterface;
use App\Shared\Http\{Request, Response};

class ExceptionMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, ?callable $next = null, ...$arguments): ?Response
    {
         try {
            return $next();
        } catch (ValidationException $e) {
            return Response::json(data: $e->errors(), message: $e->getMessage() )->send(422, [], true);
        }
    }
}