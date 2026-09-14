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

use App\DTOs\Tenants\TenantRegistrationDTO;
use App\DTOs\Tenants\TenantUpdateDTO;
use App\Middleware\PermissionsMiddleware;
use App\Shared\Attributes\Middleware;
use App\Middleware\SuperAdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Service\Tenants\TenantService;
use App\Service\Service;
use App\Shared\Attributes\Permissions;
use App\Shared\Request;
use App\Shared\Connection;
use App\Shared\Attributes\Route;
use App\Shared\Response;
use App\Exceptions\AppExceptionHandler;
use Exception;
use App\Events\Container;
use App\Events\Tenants\TenantRegistered;
use Throwable;


#[Route(path: 'tenants/')]
class TenantController extends Controller{
    /** @var TenantService  */
    protected ?Service $service = null;

    function __construct(?Connection $dbConnection = null){
        parent::__construct($dbConnection);
        $this->request = Request::instance();
        $this->service = new TenantService($dbConnection);
    }

    #[Route('GET', '/{uuid}')]
    #[Permissions(['tenants.view'])]
    public function get($uuid = '') : void {
         $response = null;
        try{
            $response = $this->service->getTenant($uuid);
            Response::json(code: 200, status: true, data: object(tenants: ($response ?: object())));
        }catch(AppExceptionHandler $exception) {
            Response::json('There was an error whilst querying for user, try again later', false, 500, object(), [], true);
        }catch(Exception $exception){
            Response::json('500 Error - Try again later', false, 500, object(), [], true);
        }
    }



    #[Route('GET', '/list')]
    #[Middleware(AuthMiddleware::class)]
    #[Permissions(['tenants.list', 'tenants.view', 'tenants.edit'])]
    public function index() : void {
        $response = null;
        try{
            $response = $this->service->list();
            Response::json(message: $response !== object() ? '' :  'Couldn\'t find any tenants',code: 200, status: true, data: object(tenants: ($response ?: object())));
        }catch(AppExceptionHandler $exception) {
            Response::json('There was an error whilst querying for the tenants, try again later', false, 500, object(), [], true);
        }catch(Exception $exception){
            Response::json('500 Error - Try again later', false, 500, object(), [], true);
        }
    }

    #[Route('POST', '/save')]
    #[Middleware(AuthMiddleware::class)]
    #[Permissions(['tenants.edit'])]
    public function store() : void {
        $response = null;
        try{
            $tenantRegisterDTO = new TenantRegistrationDTO(
                name: $this->request->post('name') ?? $this->request->post('legal_name'),
                email: $this->request->post('email'),
                slug: $this->request->post('slug'),
                domain: $this->request->post('domain'),
                type: (int)$this->request->post('type'),
                tax_id: $this->request->post('tax_id'),
                legal_name: $this->request->post('legal_name'),
                trade_name: $this->request->post('trade_name'),
                municipal_registration: $this->request->post('municipal_registration'),
                state_registration: $this->request->post('state_registration'),
                phone: is_array($this->request->post('phone')) ? implode(',', $this->request->post('phone')) : explode(',', $this->request->post('phone')),
                address: $this->request->post('address'),
                description: $this->request->post('description'),
                website: $this->request->post('website'),
                modules: $this->request->post('modules') ?? [],
                subscription_plan: (int)$this->request->post('subscriptionPlan') ?? 1,
                primaryColor: $this->request->post('customization')->primary_color,
                accentColor: $this->request->post('customization')->accent_color,
                backgroundColor: $this->request->post('customization')->background_color,
                textColor: $this->request->post('customization')->text_color,
            );

            $response = $this->service->store($tenantRegisterDTO);


            Container::getInstance()->dispatch(
                new TenantRegistered((int)$response->insertID, $tenantRegisterDTO->name, $tenantRegisterDTO->email)
            );

            Response::json(message: 'Tenant created', status: true, code: 201, bShouldExit: true, data: $response);
        }catch(Throwable $th){
            Response::log('error', $th->getMessage(), 500, false, (object)$th->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }
    }

    #[Route('PUT', '/{uuid}')]
    #[Middleware(AuthMiddleware::class)]
    #[Permissions(['tenants.edit'])]
    public function update(string $uuid) : void {
        $response = null;
        try{
            $tenantRegisterDTO = new TenantUpdateDTO(
                uuid: $uuid,
                name: $this->request->put('name') ?? $this->request->put('legal_name'),
                email: $this->request->put('email'),
                slug: $this->request->put('slug'),
                domain: $this->request->put('domain'),
                type: (int)$this->request->put('type'),
                tax_id: $this->request->put('tax_id'),
                legal_name: $this->request->put('legal_name'),
                trade_name: $this->request->put('trade_name'),
                municipal_registration: $this->request->put('municipal_registration'),
                state_registration: $this->request->put('state_registration'),
                phone: $this->request->put("phone"),
                address: $this->request->put('address'),
                description: $this->request->put('description'),
                website: $this->request->put('website'),
                modules: $this->request->put('modules') ?? [],
                subscription_plan: (int)$this->request->put('subscription_plan') ?? 1,
                primaryColor: $this->request->put('customization')->primary_color,
                accentColor: $this->request->put('customization')->accent_color,
                backgroundColor: $this->request->put('customization')->background_color,
                textColor: $this->request->put('customization')->text_color,
            );

            $response = $this->service->update($tenantRegisterDTO);


            Container::getInstance()->dispatch(
                new TenantRegistered((int)$response->insertID, $tenantRegisterDTO->name, $tenantRegisterDTO->email)
            );

            Response::json(message: 'Tenant created', status: true, code: 201, bShouldExit: true, data: $response);
        }catch(Throwable $th){
            Response::log('error', $th->getMessage(), 500, false, (object)$th->getTraceAsString());
            Response::json(message: '500 - Something went wrong, try again later', status: false, code: 500, bShouldExit: true);
        }
    }
}
