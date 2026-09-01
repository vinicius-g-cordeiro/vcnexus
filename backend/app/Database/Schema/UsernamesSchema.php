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
use App\Database\Schema\Schema;
use App\Database\Attributes\Column;
use App\Database\Attributes\Index;


#[ForeignKeyConstraint(name: 'fk_usernames_user_tenant', foreignKeys: ['user_id', 'tenant_id'], references:'users', columns: ['id', 'tenant_id'], actionOnDelete: true, deleteAction: 'CASCADE')]
#[ForeignKeyConstraint(name: 'fk_users_created_by', foreignKeys: ['created_by'], references:'users', columns:['id'], actionOnDelete: true, deleteAction: 'SET NULL')]
#[ForeignKeyConstraint(name: 'fk_users_updated_by', foreignKeys: ['updated_by'], references:'users', columns:['id'], actionOnDelete: true, deleteAction: 'SET NULL')]
#[ForeignKeyConstraint(name: 'fk_users_deleted_by', foreignKeys: ['deleted_by'], references: 'users', columns: ['id'],actionOnDelete: true, deleteAction: 'SET NULL')]
#[Index(name: 'idx_usernames_user_id', unique: false, columns:['user_id'], references:'usernames')]
#[Index(name: 'uq_usernames_tenant_username', unique: true, columns: ['tenant_id', 'username'], condition: ['active' => 'B\'1\''], references:'usernames')]
class UsernamesSchema extends Schema {
    public string $table = 'usernames';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: true, comment: '')]
    public ?string $username = null;

    #[Column(type: 'BIGINT', default: null, nullable: true, comment: '')]
    public ?string $user_id= null;

    function __construct(){
        parent::__construct();


    }
}