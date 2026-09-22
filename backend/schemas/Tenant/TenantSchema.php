<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Schemas\Tenant;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, Timestamps, Nullable, Auditable};


#[Timestamps(created_at: 'created_at', updated_at: 'updated_at', deleted_at: 'deleted_at')]
#[Auditable(created_by: 'created_by', updated_by: 'updated_by', deleted_by: 'deleted_by', deleted_reason: 'deleted_reason')]
final class TenantSchema extends AbstractSchema
{
    public string $table = 'tenants';

    #[Column(type: 'smallint', length: 1, default: 1)]
    #[Nullable(nullable: false)]
    public ?int $subscription_type;

    #[Column(type: 'smallint', length: 1, default: 1)]
    #[Nullable(nullable: false)]
    public ?int $subscription_status;
    
    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: true)]
    public string $domain;

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: false)]
    public ?string $slug;
}