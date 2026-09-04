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
        $tenantModel = new TenantModel($dbConnection);
        parent::__construct($dbConnection, new UsersSchema());
        $usernamesModel = new UsernameModel($dbConnection);
    }

    function list(?object $parameters) : object|bool|null|array {
        $response = null;
        
        $sql = 'select t.name as "organization",un.username , (select cu.name from users cu where cu.id = u.created_by limit 1) as "created_by" , u.name, u.surname, u.lastname, u.nickname, u.created_at, u.updated_at, u.created_by , u.email 
        from "' . $this->schema->table . '" u 
        inner join "tenants" t on u.tenant_id = t.id 
        inner join "usernames" un on un.user_id = u.id
        ';

        if(isset($parameters, $parameters->search) && $parameters->search !== ''){
            $sql .= 'where (public.unaccent(lower(u.name)) like public.unaccent(lower(\'%'.$parameters->search.'%\')) or public.unaccent(lower(u.surname)) like public.unaccent(lower(\'%'.$parameters->search.'%\')) 
            or public.unaccent(lower(u.lastname)) like public.unaccent(lower(\'%'.$parameters->search.'%\')) or public.unaccent(lower(un.username)) like public.unaccent(lower(\'%'.$parameters->search.'%\')) 
            or public.unaccent(lower(u.phone)) like public.unaccent(lower(\'%'.$parameters->search.'%\')) or public.unaccent(lower(u.email)) like public.unaccent(lower(\'%'.$parameters->search.'%\')))';
        }

        if(isset($parameters, $parameters->active) && $parameters->active !== ''){
            if(isset($parameters, $parameters->search) && $parameters->search !== ''){
                $sql .= ' and  u.active = ' . $parameters->active . ' ';
            }else{
                $sql .= ' where  u.active = ' . $parameters->active . ' ';
            }
        }
        try{
            $result = $this->getConnection()->Execute($sql);
            $response = $this->fr2Arr($result, false);
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }

        
        return $response;
    }

    function login(?object $parameters = null): object|bool {
        $query = $this->getConnection()->Prepare('SELECT u.id, u.uuid, u.name, u.email, u.phone, u.lastname, u.active, u.password , un.username FROM ' . $this->schema->table . ' u inner join usernames un on un.user_id = u.id  WHERE public.unaccent(lower(email)) = ? or phone = ? or public.unaccent(lower(un.username)) = public.unaccent(lower(?)) LIMIT 1;');
        $response = $this->getConnection()->Execute($query,[$parameters->login, $parameters->login, $parameters->login]);

        $result = $this->fr2Arr($response);

        if (is_bool($result) || (is_bool($result) === false && count($result) == 0)) {
            throw new AppExceptionHandler(message: 'No result found', code: 404);
        }

        if (!password_verify($parameters->password, $result[0]->password)) {
            throw new AppExceptionHandler(message: 'No result found', code: 404);
        }


        // remove the password from the response
        unset($result[0]->password);

        return (object)$result[0] ?? false;
    }

    function find(?string $uuid, array $columns = []) : object|bool {
        $returnColumns = implode(', ', $columns);
        
        $where = ' WHERE u.uuid = ?';
        $response = $this->getConnection()->Execute('SELECT ' . $returnColumns . '  FROM ' . $this->schema->table . ' u INNER JOIN tenants t ON t.id = u.tenant_id LEFT JOIN usernames un ON un.user_id = u.id ' . $where, [$uuid]);

        $result = $this->fr2Arr($response, false);
        return (object)$result[0] ?? false;
    }

}
