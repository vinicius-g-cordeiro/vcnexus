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

use App\Exceptions\AppExceptionHandler;
use App\Model\UsernameModel;
use App\Service\Service;
use App\Shared\Connection;

final class UsernameService extends Service {
    function __construct(protected ?Connection $connection = null){
        parent::__construct($connection, new UsernameModel($connection));
    }

    public function list(?object $parameters = null) : object|array|bool {
        $response = null;
        $response = $this->model->list($parameters);
        if($response === false || $response === null){
            throw new AppExceptionHandler('Could not find any username!', 404);
        }

        return $response;
    }
}