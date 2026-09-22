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

use App\Shared\Http\Attributes\{Route, Middleware, Permission};
use App\Shared\Http\Controllers\BaseController;
use App\Shared\Http\{Response, Request, Session, DTOValidator};
use App\Shared\Http\Middleware\{AuthMiddleware};

#[Route(path: '/v1/permissions')]
#[Middleware(AuthMiddleware::class)]
#[Permission(['permissions.manage'])]
final class PermissionController extends BaseController
{
    public function __construct(Request $request, Session $session, private DTOValidator $validator) {
        parent::__construct($request, $session);
    }

    #[Route('GET', '/')]
    #[Permission(['permissions.list'])]
    public function index(): ?Response {
        return Response::json(data: null, message: 'Not implemented yet')->send(501, [], true);
    }
}