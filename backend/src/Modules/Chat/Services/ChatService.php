<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Models\ChatRoom;
use App\Modules\Chat\Models\ChatRoomUsers;
use App\Modules\Chat\Repositories\ChatRepository;
use App\Shared\Domain\BaseService;
use App\Modules\Chat\Exceptions\ChatException;
use App\Modules\Chat\DTOs\{ChatUserListRequest, ChatUserListResponse, ChatConversationRequest};

final class ChatService extends BaseService
{
    public function __construct(\ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private ChatRepository $chatRepository) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    /**
     * 
     * @param ChatUserListRequest $chatUserListRequest
     * @return array<ChatUserListResponse> - available users for chat
     */
    public function getAvailableUsers(ChatUserListRequest $chatUserListRequest): array {
        return $this->chatRepository->findAvailableForChat($chatUserListRequest);
    }

    public function getOrCreateConversation(ChatConversationRequest $chatConversationRequest) : ?array {
        
        if($chatConversationRequest->user_id === $chatConversationRequest->recipient_id) {
            throw new ChatException('You cannot start a conversation with yourself');
        }

        $existingConversation = $this->chatRepository->findConversation($chatConversationRequest);

        if(isset($existingConversation)) {
            return $existingConversation;
        }

        $conversation = $this->transactional(function() use ($chatConversationRequest) {

            $chatRoomRequest = ChatRoom::fromArray(['created_at' => date('Y-m-d H:i:s'), 'tenant_id' => $this->tenant_id, 'active' => 1]);
            $newChatRoom = $this->chatRepository->store($chatRoomRequest, ['id', 'uuid', 'created_at', 'tenant_id'], 'chat_rooms');

            if($newChatRoom === false) {
                throw new ChatException('Could not create chat room');
            }
            
            $newConversationRequest = ChatRoomUsers::fromArray(['room_id' => $newChatRoom->id, 'user_id' => $chatConversationRequest->user_id]);
            
            $newConversation = $this->chatRepository->store($newConversationRequest, ['id', 'uuid'], 'chat_room_users');
            if($newConversation === false) {
                throw new ChatException('Could not create first conversation');
            }

            $newConversationRequest = ChatRoomUsers::fromArray(['room_id' => $newChatRoom->id, 'user_id' => $chatConversationRequest->recipient_id]);
            
            $newConversation = $this->chatRepository->store($newConversationRequest, ['id', 'uuid'], 'chat_room_users');
            if($newConversation === false) {
                throw new ChatException('Could not create second conversation');
            }

            return $newConversation;
        });

        if($conversation === false) {
            throw new ChatException('Could not create conversation');
        }
        return $conversation;
    }
}