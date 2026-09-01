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

use App\DTOs\Users\UserRegistrationDTO;
use App\Middleware\AdminOrOwnerMiddleware;
use App\Middleware\GuestMiddleware;
use App\Service\AuthService;
use App\Service\Service;
use App\Shared\Attributes\Middleware;
use App\Shared\Attributes\RateLimit;
use App\Shared\Request;
use App\Shared\Connection;
use App\Shared\Attributes\Route;
use App\Shared\Response;
use Exception;


class AuthController extends Controller{
    /** @var AuthService  */
    protected ?Service $service = null;

    function __construct(?Connection $dbConnection = null){
        parent::__construct($dbConnection);
        $this->request = Request::instance();
        $this->service = new AuthService($dbConnection);
    }

    
    #[Route('POST', '/user/create/')]
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
                gender: (int)$this->request->post('gender'),
                sexual_orientation: (int)$this->request->post('sexual_orientation'),
                marital_status: (int)$this->request->post('marital_status'),
                locale: $this->request->post('locale'),
                nickname: $this->request->post('nickname'),
            );

            $response = $this->service->store($userRegisterDTO);

            Response::json(message: 'User created', status: true, code: 201, bShouldExit: true, data: $response);

        }catch(Exception $er){
            Response::log('error', $er->getMessage(), 500, false, (object)$er->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }
    }

}
