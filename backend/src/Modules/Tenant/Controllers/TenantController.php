<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Tenant\Controllers;

use App\Shared\Http\Request;
use App\Shared\Http\Session;
use App\Shared\Helpers\Utils;
use App\Shared\Http\Response;
use App\Shared\Http\DTOValidator;
use App\Shared\Http\Attributes\Route;
use App\Shared\Http\Attributes\Middleware;
use App\Modules\Tenant\DTOs\TenantListRequest;
use App\Modules\Tenant\Services\TenantService;
use App\Shared\Http\Middleware\AuthMiddleware;
use App\Shared\Http\Controllers\BaseController;
use App\Modules\Tenant\DTOs\TenantStoreInRequest;
use App\Shared\Http\Middleware\TenantResolverMiddleware;

#[Route(path: '/v1/tenants')]
#[Middleware(AuthMiddleware::class)]
#[Middleware(TenantResolverMiddleware::class)]
final class TenantController extends BaseController
{
    public function __construct(Request $request, Session $session, private TenantService $service, private DTOValidator $validator) {
        parent::__construct($request, $session);
    }

    #[Route(methods: ['GET', 'QUERY'], path: '/')]
    public function index() : ?Response
    {
        try{
            $tenantListRequest = TenantListRequest::fromArray((array)$this->request->get());
            $tenants = $this->service->index($tenantListRequest);
            return Response::json(data: $tenants, message: 'success')->send(200, [], true);
        }catch(\Throwable $th){
            throw $th;
        }
    }

    #[Route(methods: ['GET', 'QUERY'], path: '/{uuid}/organization')]
    public function organization(?string $uuid) : ?Response
    {
        try{
            $tenants = $this->service->organization($uuid);
            return Response::json(data: $tenants, message: 'success')->send(200, [], true);
        }catch(\Throwable $th){
            throw $th;
        }
    }

    #[Route(methods: ['GET', 'QUERY'], path: '/{uuid}/brandings')]
    public function brandings(?string $uuid) : ?Response
    {
        try{
            $tenants = $this->service->brandings($uuid);
            return Response::json(data: $tenants, message: 'success')->send(200, [], true);
        }catch(\Throwable $th){
            throw $th;
        }
    }

    #[Route(methods: ['GET', 'QUERY'], path: '/{uuid}/addresses')]
    public function addresses(?string $uuid) : ?Response
    {
        try{
            $tenants = $this->service->addresses($uuid);
            return Response::json(data: $tenants, message: 'success')->send(200, [], true);
        }catch(\Throwable $th){
            throw $th;
        }
    }

    #[Route(methods: ['GET', 'QUERY'], path: '/{uuid}/contacts')]
    public function contacts(?string $uuid) : ?Response
    {
        try{
            $tenants = $this->service->contacts($uuid);
            return Response::json(data: $tenants, message: 'success')->send(200, [], true);
        }catch(\Throwable $th){
            throw $th;
        }
    }

    #[Route(methods: 'POST',path: '/')]
    public function store() : ?Response {
        try{
            $post = Utils::flatten((array)$this->request->params());
            $dto = TenantStoreInRequest::fromArray(['logo' => $this->request->files('logo'), ...$post]);
            $this->validator->validate($dto);
            $tenant = $this->service->store($dto);
            return Response::json(data: $tenant, message: 'success')->send(201, [], true);
        }catch(\Throwable $th){
            throw $th;
        }
    }

    #[Route(methods: ['DELETE', 'PUT'], path: '/deactivate/{id}/')]
    public function deactivate($id) : ?Response {
        try{
            $tenant = $this->service->deactivate($id);
            return Response::json(data: object(deactivated: $tenant), message: 'success')->send(200, [], true);
        }catch(\Throwable $th){
            throw $th;
        }
    }

    #[Route(methods: ['DELETE', 'PUT'], path: '/activate/{id}/')]
    public function activate($id) : ?Response {
        try{
            $tenant = $this->service->activate($id);
            return Response::json(data: object(activated: $tenant), message: 'success')->send(200, [], true);
        }catch(\Throwable $th){
            throw $th;
        }
    }
}

