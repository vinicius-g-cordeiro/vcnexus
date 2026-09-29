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

use App\Infrastructure\Redis\RedisConnectionFactory;
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
    /** Seconds a WebSocket ticket stays valid. It is single-use. */
    private const WS_TICKET_TTL = 30;

    public function __construct(Request $request, Session $session, private DTOValidator $validator, private ChatService $service) {
        parent::__construct($request, $session);
    }

    #[Route('GET', '/users/')]
    public function index(): ?Response {
        $chatUserListRequest = ChatUserListRequest::fromArray(['user_id' => $this->session->get('user')->id]);
        $this->validator->validate($chatUserListRequest);
        $users = $this->service->getAvailableUsers($chatUserListRequest);
        return Response::json(data: $users, message: 'Chat users retrieved successfully')->send(200, [], true);
    }

    /**
     * Returns {id} of the 1:1 room between the authenticated user and recipient_id (creates it if needed).
     */
    #[Route('POST', '/conversations')]
    public function show(): ?Response {
        $conversationRequest = ChatConversationRequest::fromArray([
            'user_id' => $this->session->get('user')->id,
            'recipient_id' => $this->request->post('recipient_id'),
        ]);

        $this->validator->validate($conversationRequest);
        $conversation = $this->service->getOrCreateConversation($conversationRequest);
        return Response::json(data: $conversation, message: 'Chat conversation retrieved successfully')->send(200, [], true);
    }

    /**
     * Message history. Optional query params: limit (1-100, default 50) and before (message id) for pagination.
     */
    #[Route('GET', '/rooms/{roomId}/messages/')]
    public function messages(string $roomId): ?Response {
        $limit = (int) ($this->request->get('limit') ?? 50);
        $before = $this->request->get('before');

        $messages = $this->service->getMessages(
            (int) $roomId,
            (int) $this->session->get('user')->id,
            $limit > 0 ? $limit : 50,
            $before !== null && $before !== '' ? (int) $before : null
        );

        return Response::json(data: $messages, message: 'Chat messages retrieved successfully')->send(200, [], true);
    }

    /**
     * Issues a short-lived, single-use ticket the browser presents when opening the WebSocket.
     * The WS server consumes it from Redis, which is how it learns who the connection belongs to.
     */
    #[Route('POST', '/ws-ticket')]
    public function wsTicket(): ?Response {
        $ticket = bin2hex(random_bytes(24));

        $redis = (new RedisConnectionFactory())->create();
        $redis->setEx("ws_ticket:{$ticket}", self::WS_TICKET_TTL, json_encode([
            'user_id'   => (string) $this->session->get('user')->id,
            'tenant_id' => (string) $this->request->attribute('tenant_id'),
            'roles'     => $this->request->attribute('roles'),
        ], JSON_THROW_ON_ERROR));

        return Response::json(data: ['ticket' => $ticket], message: 'WebSocket ticket issued')->send(200, [], true);
    }
}