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

use App\Modules\Authorization\Services\MenuService;
use App\Shared\Http\Attributes\{Route, Middleware};
use App\Shared\Http\Controllers\BaseController;
use App\Shared\Http\{Response, Request, Session, DTOValidator};
use App\Shared\Http\Middleware\{AuthMiddleware};

#[Route(path: '/v1/menus')]
#[Middleware(AuthMiddleware::class)]
final class MenuController extends BaseController
{
    public function __construct(Request $request, Session $session, private DTOValidator $validator, private MenuService $service) {
        parent::__construct($request, $session);
    }

    #[Route('GET', '/')]
    public function index(): ?Response {
        try{
            $menuList = $this->service->index();
            return Response::json(data: $menuList, message: 'success')->send(200, [], true);
        }catch(\Throwable $th) {
            throw $th;
        }
    }
}