<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\DTOs\Users;

use App\Database\Schema\Constraint;
use App\Database\Schema\Index;
use App\DTOs\DTO;
use App\Database\Schema\Column;

class UsersDTO extends DTO{
    public string $table = 'users';
    public Column $email {
        get {
            return $this->email ??= new Column(name: 'email', type: 'VARCHAR(100)', default: '', isNull: false, comment: '');
        }
    }

    public ?Column $phone {
        get { 
            return $this->phone ??= new Column(name: 'phone', type: 'VARCHAR(24)', default: null, isNull: true, comment: '');
        }
    }

    public Column $address {
        get {
            return $this->address ??= new Column(name: 'address', type: 'VARCHAR(500)', default: null, isNull: true, comment: '');
        }
    }
    function __construct(){
        parent::__construct();

        $this->indexes->username_index = new Index(name: 'idx_usernames_user_id', unique: false, columns: ['user_id']);
        $this->indexes->username_tenant_index = new Index(name: 'uq_usernames_tenant_username', unique:true, columns: ['tenant_id', 'username'], condition: ['active' => 'B\'1\'']);
        

        $this->constraints->unique_user_tenant_id = new Constraint(name: 'uq_users_id_tenant' ,bIsUnique: true, uniqueColumns: ['id', 'tenant_id']);
        $this->constraints->deletedBy_constraint = new Constraint(name: 'fk_users_deleted_by', foreignKeys:['deleted_by'], references:'users', columns: ['id'], actionOnDelete: true, deleteAction:'SET NULL');
        $this->constraints->tenant_username_constraint = new Constraint(name: 'fk_usernames_user_tenant', foreignKeys:['user_id', 'tenant_id'], references:'users', columns: ['id', 'tenant_id'], actionOnDelete: true, deleteAction:'CASCADE');

    }
}