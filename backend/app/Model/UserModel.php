<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Model;

use App\Database\Schema\UsersSchema;
use App\Exceptions\AppExceptionHandler;


final class UserModel extends Model
{

    function __construct($dbConnection = null)
    {
        parent::__construct($dbConnection, new UsersSchema());
    }

    function list(?object $parameters) : object|bool|null|array {
        $response = null;
        
        try{
            $result = $this->getConnection()->Execute('select t.name as "organization", u.name, u.name, u.tenant_id from "' . $this->schema->table . '" u inner join tenants t on u.tenant_id = t.id ;');
            $response = $this->fr2Arr($result, false, 'array');
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }

        
        return $response;
    }
}
