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

use App\Model\Model;
use App\Exceptions\AppExceptionHandler;
use App\Database\Schema\TenantSchema;


final class TenantModel extends Model
{

    function __construct($dbConnection = null)
    {
        parent::__construct($dbConnection, new TenantSchema());
    }

    function list(?object $parameters) : object|bool|null|array {
        $response = null;
        
        $sql = 'select t.id, t.uuid, t.modules,b.legal_name, b.trade_name, b.email, b.phone, b.website, t.slug, b.website, t.subscription_plan, t.name
                , t.active, t.status,t.created_at_local, t.updated_at_local, t.subscription_plan, t.created_at, t.updated_at, b.type, b.tax_id
                , b.municipal_registration, b.state_registration, b.description, b.categories
                from "' . $this->schema->table . '" t 
                inner join business b on b.tenant_id = t.id
                left join business_branding bb on bb.business_id = b.id';

        
        if(isset($parameters, $parameters->search) && $parameters->search !== ''){
            $sql .= ' where (public.unaccent(lower(t.name)) like public.unaccent(lower(\'%'.$parameters->search.'%\')) or public.unaccent(lower(b.phone)) like public.unaccent(lower(\'%'.$parameters->search.'%\')) 
            or public.unaccent(lower(b.email)) like public.unaccent(lower(\'%'.$parameters->search.'%\')))';
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
            $response = $this->fr2Arr($result, false, 'array');
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }

        
        return $response;
    }

     function getTenant(?string $uuid) : object|bool|null|array {
        $response = null;
        
        $sql = 'select t.id, t.uuid, t.modules, b.legal_name, b.trade_name, b.email, b.phone, b.website, t.slug, b.website, t.subscription_plan, t.name
                , t.active, t.status, t.created_at_local, t.updated_at_local, t.subscription_plan, t.created_at, t.updated_at, b.type, b.tax_id, b.municipal_registration
                , b.state_registration, b.description, b.categories
                from "' . $this->schema->table . '" t 
                inner join business b on b.tenant_id = t.id
                left join business_branding bb on bb.business_id = b.id
                where t.uuid =  \'' . $uuid . '\' and t.active = 1';

        try{
            $result = $this->getConnection()->Execute($sql);
            $response = $this->fr2Arr($result);
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }

        
        return $response;
    }


}
