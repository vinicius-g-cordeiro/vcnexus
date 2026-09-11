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
use ADOConnection;


final class BusinessModel extends Model
{

    function __construct(?ADOConnection $dbConnection = null)
    {
        $businessBranding = new BusinessBrandingModel($dbConnection);
        parent::__construct($dbConnection, new BusinessSchema());
    }

    function list(?object $parameters) : object|bool|null|array {
        $response = null;
        
        $sql = 'select b.id, b.uuid, b.legal_name, b.trade_name, b.active, b.created_at, b.updated_at
                from "' . $this->schema->table . '" b ';
        try{
            $result = $this->getConnection()->Execute($sql);
            $response = $this->fr2Arr($result, false, 'array');
        }catch(\Exception $err){
            throw new AppExceptionHandler($err->getMessage(), $err->getCode(), $err->getPrevious());
        }
        return $response;
    }

}
