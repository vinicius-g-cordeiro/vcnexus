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
use App\Shared\Schema\Attributes\{Column, Timestamps, Comment,Index, RowLevelSecurity, Policy, Auditable, TenantScoped};


#[Index(name: 'idx_roles_name', references: 'roles', columns: ['name', 'tenant_id'], condition: ['active' => '1'], unique: true)]
#[RowLevelSecurity(table: 'roles', forced: true)]
#[RowLevelSecurity(table: 'roles', forced: false)]
#[Policy(name: 'tenant_roles_isolation', table: 'roles', restrictive: true,for: 'ALL', toUser: 'app_user',
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
#[Policy(name: 'tenant_roles_permissive_isolation', table: 'roles', restrictive: false ,for: 'ALL', toUser: 'app_user',
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
#[Auditable(created_by: 'created_by', updated_by: 'updated_by')]
#[Timestamps(created_at: 'created_at', updated_at: 'updated_at', deleted_at: 'deleted_at')]
#[TenantScoped(tenant_id: 'tenant_id', nullable: true)]
final class RoleSchema extends AbstractSchema
{
    public string $table = 'roles';

    #[Column(type:'varchar', length: 255, default: null)]
    public ?string $name;

    #[Column(type:'varchar', length: 255, default: null)]
    #[Comment('This description will be used for the UI to display to the user\'s about the role. It is optional though.')]
    public ?string $description;

}
