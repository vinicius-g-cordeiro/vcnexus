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
use App\Shared\Domain\BaseRepository;


final class ChatRepository extends BaseRepository {

    /**
     * The unused Request dependency was removed so the same class can be built
     * by the HTTP container AND by the long-running WebSocket process.
     */
    public function __construct(\ADOConnection $db, string $tenant_id, ?string $user_id, ?array $roles) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    protected function table(): string
    {
        return 'chat_messages';
    }

    /**
     * @return ChatUserListResponse[]
     */
    public function findAvailableForChat(?ChatUserListRequest $userId = null) : array {
        $sql = <<<'SQL'
            SELECT
                uc.id as id,
                up.firstname as name,
                CONCAT(up.surname || ' ' || up.lastname) as surname,
                uc.status as online_status
            FROM
                user_credentials uc
            INNER JOIN user_profile up ON up.user_id = uc.id
            WHERE
                uc.active = 1 AND uc.id <> ?
            ORDER BY up.firstname, up.lastname
        SQL;

        $result = $this->scopedQuery($sql, [$userId->user_id]);

        if ($result === false || empty($result)) {
            return [];
        }

        return Hydrator::hydrateMany(ChatUserListResponse::class, $result);
    }

    /**
     * True when the user exists, is active and is visible under the current RLS context.
     */
    public function userAvailableForChat(int $userId): bool {
        $sql = <<<'SQL'
            SELECT uc.id
            FROM user_credentials uc
            INNER JOIN user_profile up ON up.user_id = uc.id
            WHERE uc.id = ? AND uc.active = 1
        SQL;

        $result = $this->scopedQuery($sql, [$userId]);

        return $result !== false && !empty($result);
    }

    /**
     * Finds the 1:1 room between two users.
     *
     * @return array<int, array<string, mixed>> empty array when there is no room yet
     */
    public function findConversation(?ChatConversationRequest $chatConversationRequest) : array {
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

        return $result === false ? [] : $result;
    }

    public function doesUserHasAccessToRoom(int $user_id, int $room_id) : bool {
        $sql = <<<'SQL'
            SELECT cru.id FROM chat_room_users cru
            WHERE cru.room_id = ? AND cru.user_id = ? AND cru.active = 1
        SQL;

        $records = $this->scopedQuery($sql, [$room_id, $user_id]);

        return $records !== false && !empty($records);
    }

    /**
     * Persists a message and returns the stored row.
     *
     * @return array{id:int, uuid:?string, room_id:int, user_id:int, content:string, created_at:?string}
     */
    public function insertMessage(int $roomId, int $userId, string $content, ?string $type = 'text'): array {
        $sql = <<<'SQL'
            INSERT INTO chat_messages (room_id, user_id, content, created_at, type)
            VALUES (?, ?, ?, ?, ?)
            RETURNING id, uuid, room_id, user_id, content, created_at, type
        SQL;

        $rows = $this->scopedQuery($sql, [$roomId, $userId, $content, date('Y-m-d H:i:s'), $type]);

        if ($rows === false || empty($rows)) {
            throw new \RuntimeException('Could not save message', 500);
        }

        return $this->normalizeMessage($rows[0]);
    }

    /**
     * Newest-first pagination, returned oldest-first so the UI can render it as-is.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findMessages(int $roomId, int $limit = 50, ?int $beforeId = null): array {
        $limit = max(1, min(100, $limit));

        $sql = 'SELECT id, uuid, room_id, user_id, content, created_at, type FROM chat_messages WHERE room_id = ? AND active = 1';
        $params = [$roomId];

        if ($beforeId !== null) {
            $sql .= ' AND id < ?';
            $params[] = $beforeId;
        }

        $sql .= ' ORDER BY id DESC LIMIT ' . $limit;

        $rows = $this->scopedQuery($sql, $params);

        if ($rows === false || empty($rows)) {
            return [];
        }

        return array_reverse(array_map(fn(array $row) => $this->normalizeMessage($row), $rows));
    }

    /**
     * @param array<string, mixed> $row
     * @return array{id:int, uuid:?string, room_id:int, user_id:int, content:string, created_at:?string}
     */
    private function normalizeMessage(array $row): array {
        return [
            'id'         => (int) $row['id'],
            'uuid'       => $row['uuid'] ?? null,
            'room_id'    => (int) $row['room_id'],
            'user_id'    => (int) $row['user_id'],
            'content'    => (string) $row['content'],
            'created_at' => $row['created_at'] ?? null,
            'type'       => $row['type'] ?? 'text',
        ];
    }
}