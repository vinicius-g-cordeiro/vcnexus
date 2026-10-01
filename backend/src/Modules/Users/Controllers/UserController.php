<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Users\Controllers;

use App\Shared\Http\Attributes\Route;
use App\Shared\Http\Attributes\Middleware;
use App\Modules\Users\Services\UserService;
use App\Shared\Http\Controllers\BaseController;
use App\Shared\Http\{Response, Request, Session, DTOValidator, Middleware\ErrorLogMiddleware};
use App\Modules\Users\DTOs\{UserListRequest, UserStoreRequest};
use App\Shared\Http\Middleware\{AuthMiddleware, TenantResolverMiddleware};


#[Route(path: '/v1/users')]
#[Middleware(ErrorLogMiddleware::class)]
final class UserController extends BaseController
{
    public function __construct(private UserService $service, public Request $request, public Session $session, private DTOValidator $validator) {
        parent::__construct($request, $session);
    }

    #[Route(methods: 'POST',path: '/')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(TenantResolverMiddleware::class)]
    public function store() : ?Response {
        try{
            /// @todo implement Idepodency key validation
            $userStoreRequest = UserStoreRequest::fromArray((array)$this->request->post());
            $this->validator->validate($userStoreRequest);
            $users = $this->service->store($userStoreRequest);
            return Response::json(data: $users)->send(201, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY',path: '/')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(TenantResolverMiddleware::class)]
    public function index() : ?Response {
        try{
            $userListRequest = UserListRequest::fromArray((array)$this->request->get());
            $users = $this->service->index($userListRequest);
            return Response::json(data: $users, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY',path: '/profile/{uuid}')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(TenantResolverMiddleware::class)]
    public function profile(string $uuid) : ?Response {
        try{
            $user = $this->service->profile($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY',path: '/credentials/{uuid}')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(TenantResolverMiddleware::class)]
    public function credentials(string $uuid) : ?Response {
        try{
            $user = $this->service->credentials($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/addresses/{uuid}')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(TenantResolverMiddleware::class)]
    public function addresses(string $uuid) : ?Response {
        try{
            $user = $this->service->addresses($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/permissions/{uuid}')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(TenantResolverMiddleware::class)]
    public function permissions(string $uuid) : ?Response {
        try{
            $user = $this->service->permissions($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/contacts/{uuid}')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(TenantResolverMiddleware::class)]
    public function contacts(string $uuid) : ?Response {
        try{
            $user = $this->service->contacts($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/consents/{uuid}')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(TenantResolverMiddleware::class)]
    public function consents(string $uuid) : ?Response {
        try{
            $user = $this->service->consents($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/sensitive/{uuid}')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(TenantResolverMiddleware::class)]
    public function sensitive(string $uuid) : ?Response {
        try{
            $user = $this->service->sensitive($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }
}