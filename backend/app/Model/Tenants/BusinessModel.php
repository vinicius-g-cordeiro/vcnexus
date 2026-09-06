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

use App\Database\Schema\BusinessSchema;
use App\Model\Model;
use App\Exceptions\AppExceptionHandler;


final class BusinessModel extends Model
{

    function __construct($dbConnection = null)
    {
        parent::__construct($dbConnection, new BusinessSchema());
    }

    function list(?object $parameters) : object|bool|null|array {
        $response = null;
        
        try{
            $result = $this->getConnection()->Execute('select b.id, b.uuid, b.name, b.active, b.status, b.created_at, b.updated_at, b.subscription_plan from "' . $this->schema->table . '" b ;');
            $response = $this->fr2Arr($result, false, 'array');
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }
        return $response;
    }

}
