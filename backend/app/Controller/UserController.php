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

use App\DTOs\Users\AvatarStoreDTO;
use App\Exceptions\AppExceptionHandler;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Service\UsernameService;
use App\Shared\Attributes\Middleware;
use App\DTOs\Authentication\UserRegistrationDTO;
use App\Shared\Attributes\Route;
use App\Shared\Response;
use App\Shared\Request;
use App\Service\UserService;
use App\Service\Service;
use App\Shared\Attributes\RateLimit;
use App\Shared\Connection;
use Exception;
use App\Events\Container;
use Throwable;
use App\Events\Auth\UserRegistered;
use App\DTOs\Authentication\ProfileUpdateDTO;
use App\Shared\Helpers\Files;

#[Route('GET', '/users')]
#[Middleware(AuthMiddleware::class)]
class UserController extends Controller
{

    /** @var UserService */
    protected ?Service $service = null; 

    protected ?Files $fileHelper = null;

    public function __construct(protected ?Connection $dbConnection = null){
        parent::__construct($dbConnection);
        $this->request = Request::instance();
        $this->service = new UserService($dbConnection);
        $this->fileHelper = new Files();
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

    #[Route('GET', '/{uuid}')]
    public function get($uuid = '') : void {
         $response = null;
        try{
            $response = $this->service->getUser($uuid);
            Response::json(code: 200, status: true, data: object(users: ($response ?: object())));
        }catch(AppExceptionHandler $exception) {
            Response::json('There was an error whilst querying for user, try again later', false, 500, object(), [], true);
        }catch(Exception $exception){
            Response::json('500 Error - Try again later', false, 500, object(), [], true);
        }
    }


    #[Route('POST', 'create/')]
    #[Middleware(AuthMiddleware::class)]
    #[Middleware(AdminMiddleware::class)]
    #[RateLimit(maxAttempts: 5, decaySeconds: 60)]
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
                nickname: $this->request->post('nickname') ?: null,
                phone: $this->request->post('phone') ?: null,
                religion: $this->request->post('religion') ?: null,
                created_by: (int)$this->session->get('user')->id ?? 1,
                tenant_id: (int)$this->request->post('business')['tenant'] ?? null,
                avatar: $this->request->post('avatar'),
            );

            $response = $this->service->store($userRegisterDTO);


            Container::getInstance()->dispatch(
                new UserRegistered((int)$response->insertID, $userRegisterDTO->name, $userRegisterDTO->email)
            );

            Response::json(message: 'User created', status: true, code: 201, bShouldExit: true, data: $response);
        }catch(Throwable $er){
            Response::log('error', $er->getMessage(), 500, false, (object)$er->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }
    }

    #[Route('POST', '/{uuid}/avatar')]
    public function uploadAvatar(string $uuid) : void {
        $response = null;
        try{
            $user = $this->service->getUser($uuid);
            $avatarResponse = $this->fileHelper->upload_files_to_folder(['.jpg', '.png', '.jpeg'], '/var/www/storage/upload/users/avatars/', '/storage/upload/users/avatars/', 'avatar', $user->username);
            $avatarStoreDTO = new AvatarStoreDTO(uuid: $uuid, avatar: $avatarResponse['avatar0']['filename'] ?? $user->avatar);
            $response = $this->service->uploadAvatar($avatarStoreDTO);
            Response::json(code: 204, status:true, data: object());
        }catch(Throwable $err){
            Response::log('error', $err->getMessage(), 500, false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }catch(Exception $err){
            Response::log('error', $err->getMessage(), $err->getCode(), false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);   
        }
    }

    #[Route('PUT', '/{uuid}')]
    public function update($uuid = '') : void {
        $response = null;
        try{
            
            $userUpdateDTO = new ProfileUpdateDTO(
                id: (int)$this->request->put('id'),
                uuid: $uuid,
                name: $this->request->put('name'),
                surname: $this->request->put('surname') ?? null,
                lastname: $this->request->put('lastname'),
                username: (string)$this->request->put('username'),
                email: $this->request->put('email'),
                password: $this->request->put('password') ?? null,
                password_confirmation: $this->request->put('password_confirmation') ?? null,
                birthdate: $this->request->put('birthdate'),
                gender: (int)($this->request->put('gender') ?: null),
                sexual_orientation: (int)($this->request->put('sexual_orientation') ?: null),
                marital_status: (int)($this->request->put('marital_status') ?: null),
                religion: (int)($this->request->put('religion') ?: null),
                locale: $this->request->put('locale') ?: null,
                nickname: $this->request->put('nickname') ?: null,
                updated_by: (int)$this->session->get('user')->id ?? 1,
                phone: $this->request->put('phone') ?? '',
                tenant_id: (int)$this->request->put('tenant') ?? null,
                avatar: $this->request->put('avatar'),
            );
            $response = $this->service->updateProfile($userUpdateDTO);
            Response::json(message: '', status: true, code: 200, bShouldExit:true, data: object(user: $response));
        }catch(Throwable $err){
            Response::log('error', $err->getMessage(), 500, false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }catch(Exception $err){
            Response::log('error', $err->getMessage(), $err->getCode(), false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);   
        }
    }

    #[Route('DELETE', '/deactivate/{uuid}')]
    #[Middleware(AdminMiddleware::class)]
    public function deactivate(string $uuid) : void {
         $response = null;
        try{
            $response = $this->service->deactivate($uuid);
            Response::json(message: 'User deactivated successfully!', status: true, code: 200, bShouldExit:true, data: object(user: $response));
        }catch(Throwable $err){
            Response::log('error', $err->getMessage(), 500, false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }catch(Exception $err){
            Response::log('error', $err->getMessage(), $err->getCode(), false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);   
        }
    }

    #[Route('PUT', '/activate/{uuid}')]
    #[Middleware(AdminMiddleware::class)]
    public function activate(string $uuid) : void {
         $response = null;
        try{
            $response = $this->service->activate($uuid);
            Response::json(message: 'User activated successfully!', status: true, code: 200, bShouldExit:true, data: object(user: $response));
        }catch(Throwable $err){
            Response::log('error', $err->getMessage(), 500, false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }catch(Exception $err){
            Response::log('error', $err->getMessage(), $err->getCode(), false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);   
        }   
    }

    #[Route('PUT', '/block/{uuid}')]
    #[Middleware(AdminMiddleware::class)]
    public function block(string $uuid) : void {
        $response = null;
        try{
            $response = $this->service->block($uuid);
            Response::json(message: 'User blocked successfully!', status: true, code: 200, bShouldExit:true, data: object(user: $response));
        }catch(Throwable $err){
            Response::log('error', $err->getMessage(), 500, false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }catch(Exception $err){
            Response::log('error', $err->getMessage(), $err->getCode(), false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);   
        }   
    }

    #[Route('PUT', '/unblock/{uuid}')]
    #[Middleware(AdminMiddleware::class)]
    public function unblock(string $uuid) : void {
        $response = null;
        try{
            $response = $this->service->unblock($uuid);
            Response::json(message: 'User unblocked successfully!', status: true, code: 200, bShouldExit:true, data: object(user: $response));
        }catch(Throwable $err){
            Response::log('error', $err->getMessage(), 500, false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }catch(Exception $err){
            Response::log('error', $err->getMessage(), $err->getCode(), false, (object)$err->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);   
        }   
    }


    #[Route('GET', '/usernames/')]
    #[Middleware(AdminMiddleware::class)]
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