<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Chat\Controllers;

use App\Modules\Chat\DTOs\ChatConversationRequest;
use App\Shared\Http\Attributes\{Route, Middleware};
use App\Shared\Http\Controllers\BaseController;
use App\Shared\Http\{Response, Request, Session, DTOValidator};
use App\Shared\Http\Middleware\{AuthMiddleware,TenantResolverMiddleware};
use App\Modules\Chat\Services\ChatService;
use App\Modules\Chat\DTOs\ChatUserListRequest;

#[Route(path: '/v1/chat')]
#[Middleware(AuthMiddleware::class)]
#[Middleware(TenantResolverMiddleware::class)]
final class ChatController extends BaseController
{
    public function __construct(Request $request, Session $session, private DTOValidator $validator, private ChatService $service) {
        parent::__construct($request, $session);
    }

    #[Route('GET', '/users/')]
    public function index(): ?Response {
        $chatUserListRequest = ChatUserListRequest::fromArray(['user_id' => $this->session->get('user')->id]);
        // Validate inputs
        $this->validator->validate($chatUserListRequest);
        $users = $this->service->getAvailableUsers($chatUserListRequest);
        return Response::json(data: $users, message: 'Chat users retrieved successfully')->send(200, [], true);
    }

    #[Route('POST', '/conversations')]
    public function show(): ?Response {
        $conversationRequest = ChatConversationRequest::fromArray(['user_id' => $this->session->get('user')->id, 'recipient_id' => $this->request->post('recipient_id')]);
        
        $this->validator->validate($conversationRequest);
        $conversations = $this->service->getOrCreateConversation($conversationRequest);
        return Response::json(data: $conversations[0], message: 'Chat conversations retrieved successfully')->send(200, [], true);
    }

    #[Route('GET', '/rooms/{roomId}/messages/')]
    public function conversations(string $roomId): ?Response {
        return Response::json(data: [], message: 'Chat conversations retrieved successfully')->send(200, [], true);
    }
}