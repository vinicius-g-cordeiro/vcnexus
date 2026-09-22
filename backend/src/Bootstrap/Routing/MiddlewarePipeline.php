<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Bootstrap\Routing;

use App\Shared\Http\Interfaces\MiddlewareInterface;
use App\Shared\Http\Request;
use App\Shared\Http\Response;

final class MiddlewarePipeline
{
    /** @param MiddlewareInterface[] $middlewares */
    public function run(Request $request, array $middlewares, callable $controller) : ?Response
    {
        foreach ($middlewares as $middleware) {
            $result = $middleware->handle($request);
            if ($result !== null) return $result;
        }
        return $controller($request);
    }
}
