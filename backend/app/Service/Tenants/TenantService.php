<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Service;

use App\Service\Service;
use App\Model\TenantModel;
use App\Shared\Connection;

use App\Model\Model;
final class TenantService extends Service {
    function __construct(protected ?Connection $connection = null,protected ?Model $model = null,protected ?Validator $validator= null){
        parent::__construct($connection, $model, $validator);

        $this->model = new TenantModel($connection);
    }

    function setTentantContext() {

    }
}