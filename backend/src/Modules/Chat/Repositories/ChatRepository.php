<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Chat\Repositories;

use App\Infrastructure\Database\Hydrator;
use App\Modules\Chat\DTOs\{ChatUserListRequest, ChatUserListResponse, ChatConversationRequest};
use App\Modules\Chat\Models\ChatRoom;
use App\Shared\Domain\BaseRepository;
use App\Shared\Http\Request;

/// GET ADODBExceptions
require_once __DIR__ . '/../../../../vendor/adodb/adodb-php/adodb-exceptions.inc.php';

final class ChatRepository extends BaseRepository {
    
    public function __construct( \ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private Request $request) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    protected function table(): string
    {
        return 'chat_messages';
    }

    /**
     * @param mixed $userId
     * @return ChatUserListResponse[]
     */
    public function findAvailableForChat(?ChatUserListRequest $userId = null) : array {
        $sql = <<<'SQL'
            SELECT
                uc.id as id,
                up.firstname as name,
                CONCAT(up.surname || ' ' || up.lastname) as surname                
            FROM
                user_credentials uc 
            INNER JOIN user_profile up ON up.user_id = uc.id
            WHERE
                uc.id <> ? AND uc.active = 1 
            ORDER BY up.firstname, up.lastname
        SQL;

        $result = $this->scopedQuery($sql, [$userId->user_id]);

        if($result === false){
            return [];
        }

        return !empty($result) && $result !== false ? Hydrator::hydrateMany(ChatUserListResponse::class, $result) : null;
    }

    /**
     * 
     * @param mixed $chatConversationRequest
     * @return array<ChatConversationRequest>|null
     */
    public function findConversation(?ChatConversationRequest $chatConversationRequest) : ?array {
        $sql = <<<'SQL'
            SELECT cr.id, cr.tenant_id, cr.created_at
            FROM chat_rooms cr 
            INNER JOIN chat_room_users cru1 ON cr.id = cru1.room_id
            INNER JOIN chat_room_users cru2 ON cr.id = cru2.room_id
            WHERE (cru1.user_id = ? AND cru2.user_id = ?) AND (
                SELECT COUNT(*) FROM chat_room_users cru 
                WHERE cru.room_id = cr.id
            ) = 2
            LIMIT 1
        SQL;

        $result = $this->scopedQuery($sql, [$chatConversationRequest->user_id, $chatConversationRequest->recipient_id]);
        if($result === false){
            return null;
        }

        return $result;
    }

    public function storeChatRoomUsers(int $room_id, int $user_id,int $recipient_id) {
        $sql = <<<'SQL'
            INSERT INTO chat_room_users 
                (room_id, user_id, active)
            VALUES (?, ?, 1), (?, ?, 1)
            RETURNING id, uuid, active, user_id, room_id
        SQL;

        $this->db->StartTrans();
        try {
            $res = $this->db->GetAll($sql, [$room_id, $user_id, $room_id, $recipient_id]);
        } catch (\Throwable $e) {
            $this->db->FailTrans();
            dd($e->getMessage());
            throw $e;
        }
        $this->db->CompleteTrans();

        return $res;
    }
}