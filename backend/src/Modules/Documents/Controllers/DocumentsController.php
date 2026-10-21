<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Documents\Controllers;

use App\Shared\Http\Request;
use App\Shared\Http\Session;
use App\Shared\Http\Response;
use App\Shared\Http\DTOValidator;
use App\Shared\Http\Attributes\Route;
use App\Shared\Http\Attributes\Middleware;
use App\Shared\Http\Middleware\AuthMiddleware;
use App\Shared\Http\Controllers\BaseController;
use App\Modules\Documents\DTOs\DocumentListRequest;
use App\Modules\Documents\Services\DocumentsService;
use App\Shared\Http\Middleware\TenantResolverMiddleware;

#[Route(path: '/v1/documents')]
#[Middleware(AuthMiddleware::class)]
#[Middleware(TenantResolverMiddleware::class)]
final class DocumentsController extends BaseController
{
    public function __construct(Request $request, Session $session, private DocumentsService $service, private DTOValidator $validator) {
        parent::__construct($request, $session);
    }

    #[Route(methods: ['QUERY', 'GET'], path: '/')]
    public function list() : ?Response {
        try{
            $documentListRequest = DocumentListRequest::fromArray((array)$this->request->get());
            $documents = $this->service->list($documentListRequest);
            return Response::json(data: $documents, message: 'success')->send(200, [], true);
        }catch(\Throwable $th){
            throw $th;
        }
    }

    #[Route(methods: ['POST'], path: '/')]
    public function store() : ?Response {
        try{
            /// @todo implement the saving of the documents.
        }catch(\Throwable $th){
            throw $th;
        }
    }

}
