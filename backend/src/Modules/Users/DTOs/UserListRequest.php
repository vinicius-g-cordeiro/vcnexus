<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Users\DTOs;

use App\Shared\Domain\QueryObjectInterface;

#[\AllowDynamicProperties]
final class UserListRequest implements QueryObjectInterface
{
    public function __construct(public ?string $search, public ?int $tenant_id, public ?int $active, public ?int $blocked, public ?string $created_at)
    {
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['search'] ?? null,
            isset($data['tenant_id']) ? (int) $data['tenant_id'] : null,
            isset($data['active']) ? (int) $data['active'] : null,
            isset($data['blocked']) ? (int) $data['blocked'] : null,
            $data['created_at'] ?? null
        );
    }
}