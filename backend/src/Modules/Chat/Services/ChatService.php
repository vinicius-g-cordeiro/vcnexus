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
    private const MAX_MESSAGE_LENGTH = 4000;

    public function __construct(\ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private ChatRepository $chatRepository) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    /**
     * @return array<ChatUserListResponse> - available users for chat
     */
    public function getAvailableUsers(ChatUserListRequest $chatUserListRequest): array {
        return $this->chatRepository->findAvailableForChat($chatUserListRequest);
    }

    /**
     * Returns the 1:1 room between the two users, creating it if needed.
     *
     * @return array{id:int}
     */
    public function getOrCreateConversation(ChatConversationRequest $chatConversationRequest) : array {

        if ($chatConversationRequest->recipient_id === null || $chatConversationRequest->recipient_id <= 0) {
            throw new ChatException('Invalid recipient', 422);
        }

        if ($chatConversationRequest->user_id === $chatConversationRequest->recipient_id) {
            throw new ChatException('You cannot start a conversation with yourself', 422);
        }

        if (!$this->chatRepository->userAvailableForChat($chatConversationRequest->recipient_id)) {
            throw new ChatException('Recipient not found', 404);
        }

        $existingConversation = $this->chatRepository->findConversation($chatConversationRequest);

        if (!empty($existingConversation)) {
            return ['id' => (int) $existingConversation[0]['id']];
        }

        $roomId = $this->transactional(function () use ($chatConversationRequest) {

            $chatRoomRequest = ChatRoom::fromArray(['created_at' => date('Y-m-d H:i:s'), 'tenant_id' => $this->tenant_id, 'active' => 1]);
            $newChatRoom = $this->chatRepository->store($chatRoomRequest, ['id', 'uuid', 'created_at', 'tenant_id'], 'chat_rooms');

            if (!$newChatRoom || !$newChatRoom->id) {
                throw new ChatException('Could not create chat room', 500);
            }

            foreach ([$chatConversationRequest->user_id, $chatConversationRequest->recipient_id] as $memberId) {
                $member = ChatRoomUsers::fromArray(['room_id' => $newChatRoom->id, 'user_id' => $memberId]);
                $stored = $this->chatRepository->store($member, ['id', 'uuid'], 'chat_room_users');

                if (!$stored) {
                    throw new ChatException('Could not add user to the conversation', 500);
                }
            }

            return $newChatRoom->id;
        });

        return ['id' => (int) $roomId];
    }

    public function doesUserHasAccessToRoom(int $user_id, int $room_id) : bool {
        return $this->chatRepository->doesUserHasAccessToRoom($user_id, $room_id);
    }

    /**
     * Validates access and persists a message.
     *
     * @return array{id:int, uuid:?string, room_id:int, user_id:int, content:string, created_at:?string}
     */
    public function saveMessage(int $roomId, int $userId, string $content): array {
        $content = trim($content);

        if ($roomId <= 0 || $content === '') {
            throw new ChatException('Invalid room or message', 422);
        }

        if (mb_strlen($content) > self::MAX_MESSAGE_LENGTH) {
            throw new ChatException('Message is too long', 422);
        }

        if (!$this->chatRepository->doesUserHasAccessToRoom($userId, $roomId)) {
            throw new ChatException('You do not have access to this room', 403);
        }

        return $this->chatRepository->insertMessage($roomId, $userId, $content);
    }

    /**
     * @return array<int, array<string, mixed>> oldest first
     */
    public function getMessages(int $roomId, int $userId, int $limit = 50, ?int $beforeId = null): array {
        if ($roomId <= 0) {
            throw new ChatException('Invalid room', 422);
        }

        if (!$this->chatRepository->doesUserHasAccessToRoom($userId, $roomId)) {
            throw new ChatException('You do not have access to this room', 403);
        }

        return $this->chatRepository->findMessages($roomId, $limit, $beforeId);
    }
}