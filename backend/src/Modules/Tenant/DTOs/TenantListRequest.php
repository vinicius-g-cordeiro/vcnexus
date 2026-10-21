<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Tenant\DTOs;

use App\Shared\Domain\QueryObjectInterface;

final class TenantListRequest implements QueryObjectInterface {

    public function __construct(public ?string $search, public ?int $active, public ?int $blocked, public ?string $created_at, public ?int $subscription_type_id, public ?int $subscription_status_id, public ?bool $paginate = true) {
    }

    public function toArray(): array {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self {
        return new self(
            search: $data['search'] ?? null,
            active: isset($data['active']) ? (int) $data['active'] : null,
            blocked: isset($data['blocked']) ? (int) $data['blocked'] : null,
            created_at: $data['created_at'] ?? null,
            subscription_type_id: isset($data['subscription_type_id']) ? (int) $data['subscription_type_id'] : null,
            subscription_status_id: isset($data['subscription_status_id']) ? (int) $data['subscription_status_id'] : null,
            paginate: isset($data['paginate']) ? (bool)$data['paginate'] : true);
    }
}
