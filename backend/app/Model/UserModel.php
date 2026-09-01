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
            $result = $this->getConnection()->Execute('select t.name as "organization", u.name, u.surname, u.lastname, u.nickname, u.email from "' . $this->schema->table . '" u inner join tenants t on u.tenant_id = t.id ;');
            $response = $this->fr2Arr($result, false, 'array');
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }

        
        return $response;
    }

    function login(?object $parameters = null): object|bool {
        $query = $this->getConnection()->Prepare('SELECT id, uuid, name, email, phone, lastname, active, password FROM ' . $this->schema->table . ' WHERE email = ? or phone = ? LIMIT 1;');
        $response = $this->getConnection()->Execute($query,[$parameters->login, $parameters->login]);

        $result = $this->fr2Arr($response);

        if (is_bool($result) || (is_bool($result) === false && count($result) == 0)) {
            throw new AppExceptionHandler(message: 'User not found', code: 404);
        }

        if (!password_verify($parameters->password, $result[0]->password)) {
            throw new AppExceptionHandler(message: 'Invalid password', code: 401);
        }


        // remove the password from the response
        unset($result[0]->password);

        return (object)$result[0] ?? false;
    }

}
