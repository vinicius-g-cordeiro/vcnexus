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
use App\Shared\Helpers\Utils;


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
            $post['emails'] = $this->contactPerTypeSplitter($post['contacts'], '1');
            $post['phones'] = $this->contactPerTypeSplitter($post['contacts'], '2');
            $userStoreRequest = UserStoreRequest::fromArray($post);
            $this->validator->validate($userStoreRequest);
            $users = $this->service->store($userStoreRequest);
            return Response::json(data: $users)->send(201, [], true);
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
            if($contact['type'] == $contactType) {
                $contactInfo[] = $contact;
            }
        }
        return $contactInfo;
    }
}