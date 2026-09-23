<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Chat\Models;

final class ChatMessage
{
    public function __construct(
        private readonly ?int $id,
        private readonly int $roomId,
        private readonly int $userId,
        private readonly string $content,
        private readonly \DateTimeImmutable $createdAt,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'room_id' => $this->roomId,
            'user_id' => $this->userId,
            'content' => $this->content,
            'created_at' => $this->createdAt->format(
                \DateTimeInterface::ATOM
            ),
        ];
    }
}