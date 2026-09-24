<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Chat\DTOs;

use App\Shared\Domain\QueryObjectInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ChatConversationRequest implements QueryObjectInterface
    {#[Assert\NotBlank]
    public readonly ?int $user_id;

    public readonly ?int $recipient_id;

    public function __construct() {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self
    {
        $newObject = new static();
        $newObject->user_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $newObject->recipient_id = isset($data['recipient_id']) ? (int) $data['recipient_id'] : null;
        return $newObject;
    }
}
