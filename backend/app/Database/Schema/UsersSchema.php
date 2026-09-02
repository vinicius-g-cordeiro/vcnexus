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

use App\Database\Attributes\ForeignKeyConstraint;
use App\Database\Attributes\UniqueConstraint;
use App\Database\Schema\Schema;
use App\Database\Attributes\Column;


#[ForeignKeyConstraint(name: 'fk_users_tenant', foreignKeys: ['tenant_id'], references: 'tenants', columns: ['id'], actionOnDelete: true, deleteAction: 'CASCADE', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_users_created_by', foreignKeys: ['created_by'], references: 'users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_users_updated_by', foreignKeys: ['updated_by'], references: 'users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_users_deleted_by', foreignKeys: ['deleted_by'], references: 'users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[UniqueConstraint(name: 'uq_users_id_tenant', columns: ['id', 'tenant_id']),]
#[UniqueConstraint(name: 'uq_users_email', columns: ['email'])]
class UsersSchema extends Schema
{
    public string $table = 'users';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: false)]
    public string $name = '';

    #[Column(type: 'VARCHAR(64)', default: '', nullable: false)]
    public string $password = '';
    
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


    #[Column(type: 'TIMESTAMP', default: null, nullable: true, comment: 'Last time the user was logged in on the system')]
    public ?string $last_login = null;

    #[Column(type: 'TIMESTAMP WITH TIME ZONE', default: null, nullable: true, comment: 'Last time the user was logged in on the system')]
    public ?string $last_login_local = null;

    #[Column(type: 'VARCHAR(50)', default: null, nullable: true, comment: '')]
    public ?string $last_ip = null;


    #[Column(type: 'VARCHAR(500)', default: null, nullable: true, comment: '')]
    public ?string $last_agent = null;

    function __construct() {
        parent::__construct();
    }
}