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
        
        try{
            $result = $this->getConnection()->Execute('select t.id as "tenant_id", t.name as "organization", t.active, t.status, t.created_at, t.updated_at, t.subscription_plan from "' . $this->schema->table . '" t ;');
            $response = $this->fr2Arr($result, false, 'array');
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }

        
        return $response;
    }

}
