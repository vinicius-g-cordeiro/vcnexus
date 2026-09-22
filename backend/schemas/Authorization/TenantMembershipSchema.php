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
use App\Shared\Schema\Attributes\{Column, Timestamps, Auditable, Unique, TenantScoped, RowLevelSecurity, Policy, ForeignKey};

#[RowLevelSecurity(table: 'tenant_memberships', forced: true)]
#[RowLevelSecurity(table: 'tenant_memberships', forced: false)]
#[Policy(name: 'tenant_memberships_access', table: 'tenant_memberships', restrictive: false,for: 'SELECT', toUser: 'app_user',
     using:  [
        '' => [
            'key' => 'app.roles',
            'type' => 'varchar[]',
            'array' => true,
            'condition' => '1', // if the user is a super admin we allow it 
            'operator' => '= ANY',
        ],
        'user_id' => [
            'key' => 'app.user_id' ,
            'type' => 'bigint',
            'nullIf' => true
        ]
    ]
)]
#[Policy(name: 'tenant_tenant_memberships_isolation', table: 'tenant_memberships', restrictive: true,for: 'ALL', toUser: 'app_user',
     using:  [
        '' => [
            'key' => 'app.roles',
            'type' => 'varchar[]',
            'array' => true,
            'condition' => '1',
            'operator' => '= ANY',
        ],
        'tenant_id' => [
            'key' => 'app.tenant_id' ,
            'type' => 'bigint',
            'nullIf' => true
        ]
    ], 
    withCheck: [
        '' => [
            'key' => 'app.roles',
            'type' => 'varchar[]',
            'array' => true,
            'condition' => '1',
            'operator' => '= ANY',
        ],
        'tenant_id' => [
            'key' => 'app.tenant_id' ,
            'type' => 'bigint',
            'nullIf' => true
        ]
    ]
)]
#[Policy(name: 'tenant_tenant_memberships_permissive_isolation', table: 'tenant_memberships', restrictive: false ,for: 'ALL', toUser: 'app_user',
     using:  [
        '' => [
            'key' => 'app.roles',
            'type' => 'varchar[]',
            'array' => true,
            'condition' => '1',
            'operator' => '= ANY',
        ],
        'tenant_id' => [
            'key' => 'app.tenant_id' ,
            'type' => 'bigint',
            'nullIf' => true
        ]
    ], 
    withCheck: [
        '' => [
            'key' => 'app.roles',
            'type' => 'varchar[]',
            'array' => true,
            'condition' => '1',
            'operator' => '= ANY',
        ],
        'tenant_id' => [
            'key' => 'app.tenant_id' ,
            'type' => 'bigint',
            'nullIf' => true
        ]
    ]
)]
#[Auditable(created_by: 'created_by', updated_by: 'updated_by', deleted_by: 'deleted_by', deleted_reason: 'deleted_reason')]
#[Timestamps(created_at: 'created_at', updated_at: 'updated_at', deleted_at: 'deleted_at')]
#[TenantScoped(tenant_id: 'tenant_id', nullable: true)]
#[Unique(name: 'tenant_membership_unique', columns: ['tenant_id', 'user_id'])]
#[ForeignKey(name: 'fk_user_address_user_credentials', foreignKeys: ['user_id'], references: 'user_credentials', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE', deferred: true)]
final class TenantMembershipSchema extends AbstractSchema
{
    public string $table = 'tenant_memberships';

    #[Column(type: 'bigint')]
    public readonly ?int $user_id;

    #[Column(type: 'bigint')]
    public readonly ?int $role_id;

    #[Column(type: 'smallint', length: 1, default: 1)]
    public readonly ?int $active;
    
    #[Column(type: 'timestamp', default: 'CURRENT_TIMESTAMP')]
    public readonly ?string $joined_at;
}