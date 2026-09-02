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

use App\DTOs\Authentication\UserRegistrationDTO;
use App\DTOs\Authentication\AuthLoginDTO;
use App\Middleware\AdminOrOwnerMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\OwnerMiddleware;
use App\Service\AuthService;
use App\Service\Service;
use App\Shared\Attributes\Middleware;
use App\Shared\Attributes\RateLimit;
use App\Shared\Request;
use App\Shared\Connection;
use App\Shared\Attributes\Route;
use App\Shared\Response;
use Throwable;


#[Route(path: 'auth/')]
class AuthController extends Controller{
    /** @var AuthService  */
    protected ?Service $service = null;

    function __construct(?Connection $dbConnection = null){
        parent::__construct($dbConnection);
        $this->request = Request::instance();
        $this->service = new AuthService($dbConnection);
    }

    
    #[Route('POST', '/register/')]
    #[Middleware(GuestMiddleware::class)]
    #[Middleware(AdminOrOwnerMiddleware::class)]
    #[RateLimit(10,60)]
    public function store() : void {
        $response = null;
        try{
            $userRegisterDTO = new UserRegistrationDTO(
                name: $this->request->post('name'),
                surname: $this->request->post('surname'),
                lastname: $this->request->post('lastname'),
                username: $this->request->post('username'),
                email: $this->request->post('email'),
                password: $this->request->post('password'),
                password_confirmation: $this->request->post('password_confirmation'),
                birthdate: $this->request->post('birthdate'),
                gender: (int)($this->request->post('gender') ?: null),
                sexual_orientation: (int)($this->request->post('sexual_orientation') ?: null),
                marital_status: (int)($this->request->post('marital_status') ?: null),
                locale: $this->request->post('locale') ?: null,
                nickname: $this->request->post('nickname') ?: null
            );

            $response = $this->service->store($userRegisterDTO);

            Response::json(message: 'User created', status: true, code: 201, bShouldExit: true, data: $response);

        }catch(Throwable $er){
            Response::log('error', $er->getMessage(), 500, false, (object)$er->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }
    }



    
    #[Route('POST', '/login/')]
    #[Middleware(GuestMiddleware::class)]
    #[Middleware(AdminOrOwnerMiddleware::class)]
    #[RateLimit(5,60)]
    public function login() : void {
        $response = null;
        try{
            $authLoginDTO = new AuthLoginDTO(
                login: $this->request->post('login'),
                password: $this->request->post('password'),
            );
            $response = $this->service->login($authLoginDTO);
            Response::json(message: 'User logged!', status: true, code: 201, bShouldExit: false, data: object(user:$response));
        }catch(Throwable $er){
            Response::log('error', $er->getMessage(), 500, false, (object)$er->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }
    }

    #[Route('GET', '/me/')]
    #[Middleware(AuthMiddleware::class)]
    public function getSelf() {
        $response = null;
        try{
            $response = $this->service->getSelf();
            Response::json(message: '', status: true, code: 200, bShouldExit:true, data: object(user: $response));
        }catch(Throwable $err){
            Response::log('error', $err->getMessage(), 500, false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }
    }

    #[Route('POST', '/logout/')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(OwnerMiddleware::class)]
    public function logout() {
        $response = null;
        try{
            $response = $this->service->logout($this->request->post('uuid') ?? null);
            Response::json(message: 'User logged out successfully', status: true, code: 200, data: object(logout: true), bShouldExit:true);
        }catch(Throwable $th){
            Response::log('error', $th->getMessage(), 500, false, (object)$th->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }
    }
}
