<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Model\Tenants;

use App\Database\Schema\TenantUsersSchema;
use App\Exceptions\AppExceptionHandler;
use App\Model\Tenants\BusinessModel;
use App\Model\Tenants\TenantModel;
use App\Model\UserModel;
use App\Model\UsernameModel;
use App\Model\Model;
use Throwable;
use App\Shared\Response;
use ADOConnection;

final class TenantUserModel extends Model
{

    function __construct(?ADOConnection $dbConnection = null)
    {
        $tenantModel = new TenantModel($dbConnection);
        $businessModel = new BusinessModel($dbConnection);
        $businessBrandingModel = new BusinessBrandingModel($dbConnection);
        $userModel = new UserModel($dbConnection);
        parent::__construct($dbConnection, new TenantUsersSchema());
        $usernamesModel = new UsernameModel($dbConnection);
    }

    function list(?object $parameters) : object|bool|null|array {
        $response = null;
        
        $sql = 'select b.trade_name as "organization",un.username , (select concat(cu.name, \' \' , cu.lastname, \' \', cu.surname) from tenant_users cu where cu.id = u.created_by limit 1) as "created_by" 
        , (select concat(cu.name, \' \' , cu.lastname, \' \', cu.surname) from tenant_users cu where cu.id = u.deleted_by limit 1) as "deleted_by", u.name, u.surname,
        (select concat(cu.name, \' \' , cu.lastname, \' \', cu.surname) from tenant_users cu where cu.id = u.blocked_by limit 1) as "blocked_by",
         u.lastname, u.nickname, u.created_at, u.updated_at, u.created_by, u.deleted_at, u.blocked, u.blocked_at, u.email, u.uuid, u.id, u.active, u.role, u.avatar
        from "' . $this->schema->table . '" u 
        inner join "tenants" t on u.tenant_id = t.id 
        inner join "business" b on b.tenant_id = t.id
        left join "business_branding" bb on bb.business_id = b.id
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


        if(isset($parameters, $parameters->blocked) && $parameters->blocked !== ''){
            if(isset($parameters, $parameters->search) && $parameters->search !== ''){
                
                if((int)$parameters->blocked === 0){
                    $sql .= ' and  u.blocked is null ';
                }else{
                    $sql .= ' and  u.blocked = ' . $parameters->blocked . ' ';
                }
            }else{
                if(isset($parameters, $parameters->activate) && $parameters->activate !== ''){
                    if((int)$parameters->blocked === 0){
                        $sql .= ' and  u.blocked is null ';
                    }else{
                        $sql .= ' and  u.blocked = ' . $parameters->blocked . ' ';
                    }
                }else{
                    if((int)$parameters->blocked === 0){
                        $sql .= ' where u.blocked is null ';
                    }else{
                        $sql .= ' where u.blocked = ' . $parameters->blocked . ' ';
                    }
                }
            }
        }


        $sql .= ' order by u.name, u.uuid desc';
        try{
            $this->setAuthContext();
            $result = $this->getConnection()->Execute($sql);
            $response = $this->fr2Arr($result, false);
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }finally{
            $this->clearAuthContext();
        }

        
        return $response;
    }

    function find(?string $uuid, array $columns = []) : object|bool {
        $returnColumns = implode(', ', $columns);
        
        $where = ' where u.uuid = \''.$uuid.'\'';
        

        $query = 'select ' . $returnColumns . '  
        from ' . $this->schema->table . ' u 
        inner join tenants t on t.id = u.tenant_id 
        inner join business b on b.tenant_id = t.id
        inner join usernames un on un.user_id = u.id 
        '  
        . $where;
        try{
            $this->setAuthContext();
            $response = $this->getConnection()->Execute($query);
        }catch(Throwable $th){
            Response::log(message: $th->getMessage());
            throw $th;
        }finally{
            $this->clearAuthContext();
        }
        $result = $this->fr2Arr($response, false);
        return (object)$result[0] ?? false;
    }

}
