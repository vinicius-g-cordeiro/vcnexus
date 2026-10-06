<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authorization\Controllers;

use App\Shared\Http\Attributes\{Route, Middleware, Permission};
use App\Shared\Http\Controllers\BaseController;
use App\Shared\Http\{Response, Request, Session, DTOValidator, Middleware\TenantResolverMiddleware};
use App\Shared\Http\Middleware\{AuthMiddleware};
use App\Modules\Authorization\Services\PermissionService;

#[Route(path: '/v1/permissions')]
#[Middleware(AuthMiddleware::class)]
#[Middleware(TenantResolverMiddleware::class)]
final class PermissionController extends BaseController
{
    public function __construct(private PermissionService $service, public Request $request, public Session $session, private DTOValidator $validator) {
        parent::__construct($request, $session);
    }

    #[Route('GET', '/')]
    #[Permission(['permissions.view'])]
    public function index(): ?Response {
        try{
            $permissions = $this->service->index();
            return Response::json(data: object(list: $permissions), message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }
}