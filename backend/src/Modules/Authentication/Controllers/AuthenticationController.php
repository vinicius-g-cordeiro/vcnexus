<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authentication\Controllers;

use App\Modules\Authentication\DTOs\{LoginRequest, LogoutRequest};
use App\Shared\Http\Controllers\BaseController;
use App\Shared\Http\Attributes\{Route, Middleware, RateLimit};
use App\Shared\Http\Middleware\{AuthMiddleware, GuestMiddleware, ErrorLogMiddleware};
use App\Shared\Http\{Response, Session, Request};
use App\Modules\Authentication\Services\AuthenticationService;

#[Middleware(ErrorLogMiddleware::class)]
#[Route(path: '/v1/auth')]
final class AuthenticationController extends BaseController
{
    public function __construct(private AuthenticationService $service, public Session $session, public Request $request) {
        parent::__construct($request, $session);
    }

    #[Route(path: '/me', methods: ['GET'])]
    #[Middleware(AuthMiddleware::class)]
    public function me() : ?Response {
        try{
            $user = $this->service->getAuthenticatedUser();
            return Response::json(data: $user)->send(200, [], true);
        }catch(\Throwable $e) {
            throw $e;
        }
    }

    #[Route(path: '/login', methods: 'POST')]
    #[Middleware(GuestMiddleware::class)]
    #[RateLimit(maxAttempts: 20, decaySeconds: 60)]
    public function login() : ?Response {
        try{
            $user = $this->service->login(new LoginRequest(
                login: $this->request->post('login'),
                password: $this->request->post('password')
            ));
            
            return Response::json(data: $user)->send(200, [], true);
        }catch(\Throwable $e) {
            throw $e;
        }
    }

    #[Route(path: '/logout', methods: 'POST')]
    #[Middleware(AuthMiddleware::class)]
    public function logout() : ?Response {
        try{
            $logoutRequest = LogoutRequest::fromArray((array)$this->request->post());
            $successfullyLoggedOut = $this->service->logout($logoutRequest);
            return Response::json(data: object(loggedOut: $successfullyLoggedOut))->send(200, [], true);
        }catch(\Throwable $e) {
            throw $e;
        }
    }
}