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

use App\Database\Schema\UsernamesSchema;
use App\Exceptions\AppExceptionHandler;

final class UsernameModel extends Model {

    function __construct($dbConnection = null){
        parent::__construct($dbConnection, new UsernamesSchema());
    }


    function list(?object $parameters) : object|bool|null|array {
        $response = null;
        $where = '';
        if(isset($parameters->username) === true){
            $where .= ' WHERE un.username like \'%' . $parameters->username . '%\'';
        }
        try{
            $result = $this->getConnection()->Execute('select un.username, un.uuid, un.id, u.name from "' . $this->schema->table . '" un inner join users u on u.id = un.user_id ' . $where . ';');
            $response = $this->fr2Arr($result, false);
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }

        
        return $response === false ? null: $response;
    }


}
