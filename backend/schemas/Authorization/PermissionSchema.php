<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Schemas\Authorization;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Auditable, Column, Nullable, Timestamps, TenantScoped};

#[Auditable(created_by: 'created_by', updated_by: 'updated_by')]
#[Timestamps(created_at: 'created_at', updated_at: 'updated_at', deleted_at: null)]
#[TenantScoped(tenant_id: 'tenant_id', nullable: true)]
final class PermissionSchema extends AbstractSchema
{
    public string $table = 'permissions';

    #[Column(type: 'varchar', length: 255)]
    #[Nullable(nullable: false)]
    public string $name;

    #[Column(type: 'varchar', length: 500, default: null)]
    #[Nullable(nullable: true)]
    public ?string $description;

    #[Column(type: 'varchar', length: 100)]
    #[Nullable(nullable: false)]
    public string $slug;


}

