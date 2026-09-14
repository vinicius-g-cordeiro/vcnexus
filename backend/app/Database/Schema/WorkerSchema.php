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

#[ForeignKeyConstraint(name: 'fk_tenant_users_created_by', foreignKeys: ['created_by'], references: 'tenant_users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_tenant_users_updated_by', foreignKeys: ['updated_by'], references: 'tenant_users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_tenant_users_deleted_by', foreignKeys: ['deleted_by'], references: 'tenant_users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[UniqueConstraint(name: 'uq_tenant_users_id_tenant', columns: ['id', 'tenant_id']),]
final class WorkerSchema extends Schema
{
    public string $table = 'workers';


    #[Column(type: 'BIGINT', default: 0, nullable: false)]
    public ?int $user_id = 1;

    #[Column(type: 'BIGINT', default: 1, nullable: false, inherit: true)]
    public ?int $tenant_id = 1;

    function __construct() {
        parent::__construct();
    }
}