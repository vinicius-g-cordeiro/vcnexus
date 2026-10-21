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

final class BusinessViewResponse implements QueryObjectInterface
{

    public function __construct(
        public ?int $id,
        public ?string $uuid,
        public ?string $fantasy_name,
        public ?string $trade_name,
        public ?string $tax_id,
        public ?string $website,
        public ?string $domain,
        public ?string $slug,
        public ?string $state_registration,
        public ?string $municipal_registration,
        public ?string $created_at,
        public ?int $active,
        public ?string $subscription_type,
        public ?string $subscription_status,
        public ?int $subscription_type_id,
        public ?int $subscription_status_id,
        public ?string $logo,
        public ?int $legal_entity_type_id,
        public ?int $type_id,
    ) {
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int)$data['id'] : null,
            uuid: $data['uuid'] ?? null,
            fantasy_name: $data['fantasy_name'] ?? null,
            trade_name: $data['trade_name'] ?? null,
            tax_id: $data['tax_id'] ?? null,
            website: $data['website'] ?? null,
            domain: $data['domain'] ?? null,    
            slug: $data['slug'] ?? null,
            state_registration: $data['state_registration'] ?? null,
            municipal_registration: $data['municipal_registration'] ?? null,
            created_at: $data['created_at'] ?? null,
            active: isset($data['active']) ? (int)$data['active'] : null,
            subscription_type: $data['subscription_type'] ?? null,
            subscription_status: $data['subscription_status'] ?? null,
            subscription_type_id: isset($data['subscription_type_id']) ? (int)$data['subscription_type_id'] : null,
            subscription_status_id: isset($data['subscription_status_id']) ? (int)$data['subscription_status_id'] : null,
            logo: $data['logo'] ?? null,
            legal_entity_type_id: $data['legal_entity_type_id'] ?? null,
            type_id: isset($data['type_id']) ? (int)$data['type_id'] : null
        );
    }
}
