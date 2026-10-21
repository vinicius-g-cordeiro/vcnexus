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

use App\Shared\Http\Request;
use App\Shared\Http\Session;
use App\Shared\Helpers\Utils;
use App\Shared\Http\Response;
use App\Shared\Http\DTOValidator;
use App\Shared\Http\Attributes\Route;
use App\Shared\Http\Attributes\Middleware;
use App\Modules\Users\DTOs\UserListRequest;
use App\Modules\Users\Services\UserService;
use App\Modules\Users\DTOs\UserStoreRequest;
use App\Shared\Http\Middleware\AuthMiddleware;
use App\Shared\Http\Controllers\BaseController;
use App\Shared\Http\Middleware\ErrorLogMiddleware;
use App\Shared\Http\Middleware\TenantResolverMiddleware;

#[Route(path: '/v1/users')]
#[Middleware(ErrorLogMiddleware::class)]
#[Middleware(AuthMiddleware::class)]
#[Middleware(TenantResolverMiddleware::class)]
final class UserController extends BaseController
{
    public function __construct(private UserService $service, public Request $request, public Session $session, private DTOValidator $validator) {
        parent::__construct($request, $session);
    }

    #[Route(methods: 'POST',path: '/')]
    public function store() : ?Response {
        try{
            /// @todo implement Idepodency key validation
            $post = (array)$this->request->params(); // But params is the one that is getting the information on this specific request, instead of the post
            // flat the array to one level
            $post = Utils::flatten($post);

            if(isset($post['contacts'])) {
                $post['emails'] = $this->contactPerTypeSplitter($post['contacts'], '1');
                $post['phones'] = $this->contactPerTypeSplitter($post['contacts'], '2');
            }
            $post['avatar'] = $this->request->files('avatar');
            $userStoreRequest = UserStoreRequest::fromArray($post);
            $this->validator->validate($userStoreRequest);
            $users = $this->service->store($userStoreRequest);
            return Response::json(data: $users)->send(201, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'DELETE',path: '/deactivate/{uuid}')]
    public function deactivate(string $uuid) : ?Response {
        try{
            $user = $this->service->deactivate($uuid);
            return Response::json(data: object(deactivated: $user), message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'PUT',path: '/activate/{uuid}')]
    public function activate(string $uuid) : ?Response {
        try{
            $user = $this->service->activate($uuid);
            return Response::json(data: object(activated: $user), message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'PUT',path: '/block/{uuid}')]
    public function block(string $uuid) : ?Response {
        try{
            $user = $this->service->block($uuid);
            return Response::json(data: object(blocked: $user), message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'PUT',path: '/unblock/{uuid}')]
    public function unblock(string $uuid) : ?Response {
        try{
            $user = $this->service->unblock($uuid);
            return Response::json(data: object(unblocked: $user), message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY',path: '/')]
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
    public function profile(string $uuid) : ?Response {
        try{
            $user = $this->service->profile($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY',path: '/credentials/{uuid}')]
    public function credentials(string $uuid) : ?Response {
        try{
            $user = $this->service->credentials($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/addresses/{uuid}')]
    public function addresses(string $uuid) : ?Response {
        try{
            $user = $this->service->addresses($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/contacts/{uuid}')]
    public function contacts(string $uuid) : ?Response {
        try{
            $user = $this->service->contacts($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/consents/{uuid}')]
    public function consents(string $uuid) : ?Response {
        try{
            $user = $this->service->consents($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/sensitive/{uuid}')]
    public function sensitive(string $uuid) : ?Response {
        try{
            $user = $this->service->sensitive($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    #[Route(methods: 'QUERY', path: '/education/{uuid}')]
    public function education(string $uuid) : ?Response {
        try{
            $user = $this->service->education($uuid);
            return Response::json(data: $user, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }

    public function contactPerTypeSplitter(array $contacts, string $contactType) : array {
        $contactInfo = [];
        foreach($contacts as $contact) {
            if($contact['type_id'] == $contactType) {
                $contactInfo[] = $contact;
            }
        }
        return $contactInfo;
    }
}
