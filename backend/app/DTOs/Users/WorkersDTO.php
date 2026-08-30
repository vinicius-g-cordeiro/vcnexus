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

use App\DTOs\DTO;
use App\Database\Schema\Column;

class WorkersDTO extends DTO{
    public string $table = 'workers';
    
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
    }
}