<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Database\Schema;

use App\Database\Attributes\FunctionAtt;
use App\Database\Attributes\Policy;
use App\Database\Attributes\ForeignKeyConstraint;
use App\Database\Attributes\UniqueConstraint;
use App\Database\Schema\Schema;
use App\Database\Attributes\Column;
use App\Database\Attributes\RowLevelSecurity;


#[ForeignKeyConstraint(name: 'fk_users_tenant', foreignKeys: ['tenant_id'], references: 'tenants', columns: ['id'], actionOnDelete: true, deleteAction: 'CASCADE', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_tenant_users_created_by', foreignKeys: ['created_by'], references: 'tenant_users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_tenant_users_updated_by', foreignKeys: ['updated_by'], references: 'tenant_users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_tenant_users_deleted_by', foreignKeys: ['deleted_by'], references: 'tenant_users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[UniqueConstraint(name: 'uq_tenant_users_id_tenant', columns: ['id', 'tenant_id']),]
#[UniqueConstraint(name: 'uq_tenant_users_email', columns: ['email'])]
#[Policy(name: 'tenant_membership', table: 'tenant_users', restrictive: false,for: 'SELECT', toUser: 'app_user',
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
#[Policy(name: 'tenant_isolation', table: 'tenant_users', restrictive: true,for: 'ALL', toUser: 'app_user',
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
#[Policy(name: 'tenant_update_isolation', table: 'tenant_users', restrictive: false,for: 'UPDATE', toUser: 'app_user',
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
#[Policy(name: 'tenant_insert_isolation', table: 'tenant_users', restrictive: false,for: 'INSERT', toUser: 'app_user',
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
#[Policy(name: 'tenant_delete_isolation', table: 'tenant_users', restrictive: false,for: 'DELETE', toUser: 'app_user',
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
#[RowLevelSecurity(table: 'tenant_users', forced: true)]
#[RowLevelSecurity(table: 'tenant_users', forced: false)]
#[FunctionAtt(name: 'get_user_tenants', tsql: 'CREATE OR REPLACE FUNCTION public.get_user_tenants(p_user_id BIGINT)
RETURNS TABLE (
    tenant_id BIGINT,
    roles VARCHAR[]
)
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
AS $$
    SELECT
        tu.tenant_id,
        tu.roles
    FROM public.tenant_users AS tu
    WHERE tu.user_id = p_user_id;
$$;

REVOKE ALL
ON FUNCTION public.get_user_tenants(BIGINT)
FROM PUBLIC;

GRANT EXECUTE
ON FUNCTION public.get_user_tenants(BIGINT)
TO app_user;')]
class TenantUsersSchema extends Schema
{
    public string $table = 'tenant_users';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: false)]
    public string $name = '';

    #[Column(type: 'BIGINT', default: 0, nullable: false)]
    public ?int $user_id = 1;

    #[Column(type: 'BIGINT', default: 1, nullable: false, inherit: true)]
    public ?int $tenant_id = 1;
    
    #[Column(type: 'VARCHAR(100)', default: '', nullable: true)]
    public ?string $surname = null;

    #[Column(type: 'VARCHAR(100)', default: '', nullable: true)]
    public ?string $lastname = null;

    #[Column(type: 'VARCHAR(100) ARRAY', default: 'ARRAY[\'\']::VARCHAR(100)[]', nullable: true, comment: '')]
    public ?string $nickname = null;

    #[Column(type: 'DATE', default: '', nullable: true)]
    public ?string $birthdate = null;

    #[Column(type: 'VARCHAR(100)', default: '', nullable: true, comment: '')]
    public ?string $email = null;

    #[Column(type: 'VARCHAR(24)', default: null, nullable: true, comment: '')]
    public ?string $phone = null;

    #[Column(type: 'SMALLINT', default: null, nullable: true, comment: '')]
    public ?int $gender = null;

    #[Column(type: 'SMALLINT', default: null, nullable: true, comment: '')]
    public ?int $marital_status = null;

    #[Column(type: 'SMALLINT', default: null, nullable: true, comment: '')]
    public ?int $sexual_orientation = null;

    #[Column(type: 'SMALLINT', default: null, nullable: true, comment: '')]
    public ?int $religion = null;

    #[Column(type: 'SMALLINT', default: null, nullable: true, comment: '')]
    public ?int $blocked = null;

    #[Column(type: 'BIGINT', default: null, nullable: true, comment: '')]
    public ?string $blocked_by = null;

    #[Column(type: 'TIMESTAMP', default: null, nullable: true, comment: '')]
    public ?string $blocked_at = null;


    #[Column(type: 'SMALLINT', default: null, nullable: true, comment: '')]
    public ?string $blood_type = null;


    #[Column(type: 'CHAR(2)', default: null, nullable: true, comment: '')]
    public ?string $blood_factor = null;


    #[Column(type: 'VARCHAR(6)', default: null, nullable: true, comment: '')]
    public ?string $locale = null;

    #[Column(type: 'VARCHAR(500)', default: null, nullable: true, comment: 'Filepath for the avatar of the user')]
    public ?string $avatar = null;


    #[Column(type: 'SMALLINT', default: 4, nullable: false, comment: '')]
    public ?int $role = 4;


    #[Column(type: 'VARCHAR(100) ARRAY', default: 'ARRAY[4]::VARCHAR(100)[]', nullable: false, comment: '')]
    public ?array $roles = [];


    #[Column(type: 'VARCHAR(100) ARRAY', default: 'ARRAY[]::VARCHAR(100)[]', nullable: false, comment: '')]
    public ?array $permissions = [];

    function __construct() {
        parent::__construct();
    }
}