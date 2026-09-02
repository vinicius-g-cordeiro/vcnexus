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

use App\Exceptions\AppExceptionHandler;
use App\Middleware\AuthMiddleware;
use App\Service\UsernameService;
use App\Shared\Attributes\Middleware;
use App\Shared\Attributes\Route;
use App\Shared\Response;
use App\Shared\Request;
use App\Service\UserService;
use App\Service\Service;
use App\Shared\Attributes\RateLimit;
use App\Shared\Connection;
use Exception;


#[Route('GET', '/users/')]
#[Middleware(AuthMiddleware::class)]
class UserController extends Controller
{

    /** @var UserService */
    protected ?Service $service = null; 
    public function __construct(protected ?Connection $dbConnection = null){
        parent::__construct($dbConnection);
        $this->request = Request::instance();
        $this->service = new UserService($dbConnection);
    }

    #[Route('GET', '/list/')]
    public function index(?Request $request) : void {
        $response = null;
        try{
            $response = $this->service->list();
            Response::json(code: 200, status: true, data: object(users: ($response ?: object())));
        }catch(AppExceptionHandler $exception) {
            Response::json('There was an error whilst querying for user, try again later', false, 500, object(), [], true);
        }catch(Exception $exception){
            Response::json('500 Error - Try again later', false, 500, object(), [], true);
        }
    }

    #[Route('GET', '/{id}/')]
    public function show(string $id) : void {
        
    }


    #[Route('POST|POST', '/create/')]
    #[RateLimit(maxAttempts: 5, decaySeconds: 60)]
    public function store(Request $request) : void {
        
    }

    #[Route('PUT', '/{id}/update/')]
    public function update(Request $request, string $id) : void {
        
    }

    #[Route('DELETE', '/{id}/delete/')]
    public function deactivate(string $id) : void {
        
    }

    #[Route('PUT|GET|PATCH', '/{id}/activate/')]
    public function activate(string $id) : void {
        
    }

    #[Route('PUT|GET|PATCH', '/{id}/block/')]
    public function block(string $id) : void {
        
    }

    #[Route('GET', '/usernames/')]
    public function getUsername() : void {
        $usernameService = new UsernameService($this->dbConnection);
        try{
            $params = $this->request->params();
            $response = $usernameService->list($params);
            Response::json(code: 200, status:true, data: $response);
        }catch(Exception $err){
            Response::json($err->getMessage(), false, $err->getCode(), object(), [], true);
        }
    }


}