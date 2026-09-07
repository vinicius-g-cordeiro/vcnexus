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

use App\Database\Schema\BusinessBrandingSchema;
use App\Model\Model;
use App\Exceptions\AppExceptionHandler;


final class BusinessBrandingModel extends Model
{

    function __construct($dbConnection = null)
    {
        parent::__construct($dbConnection, new BusinessBrandingSchema());
    }

    function list(?object $parameters) : object|bool|null|array {
        $response = null;
        
        $sql = 'select bb.id, bb.uuid, bb.legal_name, bb.trade_name, bb.active, bb.created_at, bb.updated_at
                from "' . $this->schema->table . '" bb ';
        try{
            $result = $this->getConnection()->Execute($sql);
            $response = $this->fr2Arr($result, false, 'array');
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }
        return $response;
    }

}
