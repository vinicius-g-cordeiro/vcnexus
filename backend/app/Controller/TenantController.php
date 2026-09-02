<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Controller;

use App\Service\Tenants\TenantService;
use App\Service\Service;
use App\Shared\Request;
use App\Shared\Connection;
use App\Shared\Attributes\Route;
use App\Shared\Response;
use App\Exceptions\AppExceptionHandler;
use Exception;

#[Route(path: 'tenants/')]
class TenantController extends Controller{
    /** @var TenantService  */
    protected ?Service $service = null;

    function __construct(?Connection $dbConnection = null){
        parent::__construct($dbConnection);
        $this->request = Request::instance();
        $this->service = new TenantService($dbConnection);
    }

    #[Route('GET', '/list')]
    public function index() : void {
        $response = null;
        try{
            $response = $this->service->list();
            Response::json(message: $response !== object() ? '' :  'Couldn\'t find any tenants',code: 200, status: true, data: $response ?: object());
        }catch(AppExceptionHandler $exception) {
            Response::json('There was an error whilst querying for user, try again later', false, 500, object(), [], true);
        }catch(Exception $exception){
            Response::json('500 Error - Try again later', false, 500, object(), [], true);
        }
    }

    public function store() : void {
    
    }
}
