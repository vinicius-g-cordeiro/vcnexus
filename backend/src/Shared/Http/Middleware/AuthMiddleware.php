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


use App\Modules\Authentication\Services\AuthenticationService;
use App\Shared\Http\Interfaces\MiddlewareInterface;
use App\Shared\Http\{Request, Response};

class AuthMiddleware implements MiddlewareInterface {
    
    public function __construct(private AuthenticationService $service) {
        
    }

    public function handle(Request $request, ?callable $next = null, ...$arguments) : ?Response {
        
        $user = $this->service->getAuthenticatedUser();

        if ($user === null) {
            Response::json(data: [], message: 'Invalid credentials')->send(401, [], true);
        }

        $request = $request->withAttribute('user_id', (string)$user->id);

        error_log(sprintf("%s - Authenticated user: %s with Request attribute user_id with value of %s", __METHOD__, $user->id, (string)$request->attribute('user_id')), 1);

        return $next($request);
    }
}