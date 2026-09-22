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

final class TenantListResponse implements QueryObjectInterface
{

    public function __construct( public ?string $uuid, public ?string $fantasy_name, public ?string $trade_name, public ?string $tax_id, public ?string $website, public ?string $domain, public ?string $slug,
                                public ?string $state_registration, public ?string $municipal_registration, public ?string $created_at, public ?int $active, public ?int $subscription_type,
                                public ?int $subscription_status
    ) {}

    public function toArray(): array{
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self{
        return new self($data['uuid'], $data['fantasy_name'], $data['trade_name'], $data['tax_id'], $data['website'], $data['domain'], $data['slug'],
                        $data['state_registration'], $data['municipal_registration'], $data['created_at'], (int)$data['active'], (int)$data['subscription_type'], 
                        (int)$data['subscription_status']);
    }
}