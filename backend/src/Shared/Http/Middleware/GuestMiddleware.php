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

use App\Shared\Http\{Request, Response, Session};
use App\Shared\Http\Interfaces\MiddlewareInterface;

class GuestMiddleware implements MiddlewareInterface {

    public function __construct(private Session $session) {

    }
    public function handle(Request $request,?callable $next = null, ...$arguments) : Response {
        $sessionUser = $this->session->get('user');
        if(isset($sessionUser) === true){
            return Response::json(message: '403 Forbidden', data: object())->send(403, [], true)->send(403, [], true);
        }
        return $next($request);
    }
}