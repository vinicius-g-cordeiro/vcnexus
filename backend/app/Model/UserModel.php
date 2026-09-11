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
use App\Model\Tenants\BusinessBrandingModel;
use App\Model\Tenants\BusinessModel;
use App\Model\Tenants\TenantModel;
use App\Shared\Helpers\Utils;
use Throwable;
use App\Shared\Response;
use App\Shared\Session;
use ADOConnection;

final class UserModel extends Model
{

    function __construct(?ADOConnection $dbConnection = null)
    {
        $tenantModel = new TenantModel($dbConnection);
        $businessModel = new BusinessModel($dbConnection);
        $businessBrandingModel = new BusinessBrandingModel($dbConnection);
        parent::__construct($dbConnection, new UsersSchema());

    }

    function login(?object $parameters = null): object|bool {
        
        $query = 'SELECT u.email, u.id, u.password, u.username, u.blocked, u.active
        FROM ' . $this->schema->table . ' u 
        WHERE 
        (public.unaccent(lower(u.email)) = public.unaccent(lower(?)) 
        or u.phone = ? 
        or public.unaccent(lower(u.username)) = public.unaccent(lower(?)))
        and u.active = 1 
         LIMIT 1;' ;
        
        $response = $this->getConnection()->Execute($query,[$parameters->login, $parameters->login, $parameters->login]);

        $result = $this->fr2Arr($response);
        

        if (is_bool($result) || (is_bool($result) === false && count($result) == 0)) {
            throw new AppExceptionHandler(message: 'No result found', code: 404);
        }

        if(isset($result[0]) & $result[0]->blocked !== null){
            throw new AppExceptionHandler(message: 'User is blocked', code: 403);
        }

        if(isset($result[0]) & (int)$result[0]->active !== 1){
            throw new AppExceptionHandler(message: 'User is deactivated', code: 403);
        }

        
        if (!password_verify($parameters->password, $result[0]->password)) {
            throw new AppExceptionHandler(message: 'No result found', code: 404);
        }


        $query = 'select * from get_user_tenants(\'' . $result[0]->id . '\')';
        $response = $this->getConnection()->Execute($query);
        
        $resultTenantID = $this->fr2Arr($response);
        
        if (is_bool($resultTenantID) || (is_bool($resultTenantID) === false && count($resultTenantID) == 0)) {
            throw new AppExceptionHandler('No Result Found!', 404);
        }

        $this->getConnection()->StartTrans();
        try{
   
            $contextUserID = $this->getConnection()->Execute(
                "SELECT set_config('app.user_id', ?, true), set_config('app.tenant_id', ?, false), set_config('app.roles', ?, false)",
                [$result[0]->id, $resultTenantID[0]->tenant_id, Utils::PhpArrayToPg($resultTenantID[0]->roles)]
            );

            if($this->getConnection()->HasFailedTrans()){
                throw new AppExceptionHandler('Could not failed trans');
            }
        }catch(Throwable $err){
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();
        }

        $this->getConnection()->CompleteTrans();


        // Now we check the tenant_users table for the user with the given id as we have valid credentials 
        $query = 'SELECT b.trade_name as "organization",un.username , (select concat(cu.name, \' \' , cu.lastname, \' \', cu.surname) from tenant_users cu where cu.id = u.created_by limit 1) as "created_by" 
        , (select concat(du.name, \' \' , du.lastname, \' \', du.surname) from tenant_users du where du.id = u.deleted_by limit 1) as "deleted_by", u.name, u.surname
        , (select concat(bu.name, \' \' , bu.lastname, \' \', bu.surname) from tenant_users bu where bu.id = u.blocked_by limit 1) as "blocked_by"
        , u.lastname, u.nickname, u.created_at, u.updated_at, u.created_by, u.deleted_at, u.blocked, u.blocked_at, u.email, u.uuid, u.id, u.active
        , u.role, u.avatar, u.roles, u.permissions, u.user_id, u.tenant_id
        from tenant_users u 
        left join "tenants" t on u.tenant_id = t.id 
        left join "business" b on b.tenant_id = t.id
        left join "business_branding" bb on bb.business_id = b.id
        left join "usernames" un on un.user_id = u.id
        WHERE u.user_id = ? AND u.active = ?::bigint';

        $this->getConnection()->StartTrans();
        try{
            $response = $this->getConnection()->Execute($query, [$result[0]->id, '1']);
            
            $result = $this->fr2Arr($response);

            if($this->getConnection()->HasFailedTrans()){
                throw new AppExceptionHandler('Could not failed trans');
            }

            $this->getConnection()->CompleteTrans();

        }catch(Throwable $err){
            dd($err);
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();
        }finally{
            $contextUserID = $this->getConnection()->Execute("SELECT set_config('app.user_id', '', false)",);
        }

        
        // remove the password from the response
        if(isset($result[0]->password)){
            unset($result[0]->password);
        }

        return (object)$result[0] ?? false;
    }

    /**
     * auth/me/ <br/>
     * Function called to get the current user or the user with given uuid with the specified columns. 
     * @param mixed $uuid
     * @param array $columns
     * @return bool|object
     */
    function find(?string $uuid, array $columns = []) : object|bool {
        $returnColumns = implode(', ', $columns);
        
        $where = ' where u.uuid = \''.$uuid.'\'';
        
        $query = 'select ' . $returnColumns . '
        from tenant_users u 
        inner join users uu on uu.id = u.user_id
        inner join tenants t on t.id = u.tenant_id 
        inner join business b on b.tenant_id = t.id
        inner join usernames un on un.user_id = u.id 
        '  
        . $where;
        
        try{

            $session = Session::getInstance();
            $contextUserID = $this->getConnection()->Execute(
                "SELECT set_config('app.user_id', ?, false), set_config('app.tenant_id', ?, false), set_config('app.roles', ? , false)",
                [$session->get('user')->id, $session->get('user')->tenant_id, Utils::PhpArrayToPg($session->get('user')->roles??[]) ]
            );
            
            $response = $this->getConnection()->Execute($query);
        }catch(Throwable $th){
            Response::log(message: $th->getMessage());
            throw $th;
        }finally{
            $contextUserID = $this->getConnection()->Execute(
                "SELECT set_config('app.user_id', '', true), set_config('app.tenant_id', '', false), set_config('app.roles', '' , false)",
            );
        }        
        $result = $this->fr2Arr($response, false);
        return (object)$result[0] ?? false;
    }

}
