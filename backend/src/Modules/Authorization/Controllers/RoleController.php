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
use App\Shared\Http\{Response, Request, Session, DTOValidator};
use App\Shared\Http\Middleware\{AuthMiddleware, TenantResolverMiddleware};
use App\Modules\Authorization\Services\RoleService;


#[Route(path: '/v1/roles')]
#[Middleware(AuthMiddleware::class)]
#[Middleware(TenantResolverMiddleware::class)]
final class RoleController extends BaseController
{
    public function __construct(private RoleService $service, Request $request, Session $session, private DTOValidator $validator) {
        parent::__construct($request, $session);
    }

    #[Route('GET', '/')]
    #[Permission(['roles.view'])]
    public function index(): ?Response {
        try{
            $roles = $this->service->index();
            return Response::json(data: object(list: $roles), message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }
}