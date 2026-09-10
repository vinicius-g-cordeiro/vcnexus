<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Service\Tenants;

use App\DTOs\Business\Branding\BusinessBrandingStoreDTO;
use App\DTOs\DTOInterface;
use App\DTOs\Business\BusinessRegistrationDTO;
use App\DTOs\Tenants\TenantRegistrationDTO;
use App\Exceptions\AppExceptionHandler;
use App\Model\Tenants\BusinessBrandingModel;
use App\Model\Tenants\BusinessModel;
use App\Service\Service;
use App\Model\Tenants\TenantModel;
use App\Shared\Connection;
use App\Model\Model;

use App\Shared\Helpers\Utils;
use Symfony\Component\Validator\Constraints as Assert;

final class TenantService extends Service {
    protected ?BusinessModel $businessModel = null;
    protected ?BusinessBrandingModel $businessBrandingModel = null;

    /** @var TenantModel */
    protected ?Model $model = null;
    function __construct(protected ?Connection $connection = null){
        parent::__construct($connection, new TenantModel($connection));

        $this->businessModel = new BusinessModel($this->connection);
        $this->businessBrandingModel = new BusinessBrandingModel($this->connection);
    }

    public function list(?object $parameters = null) : object|array|bool {
        $response = null;

        $parameters ??= $this->request->params();
        
        $response = $this->model->list($parameters);
        return $response ?? object();
    }

    public function store(?TenantRegistrationDTO $tenantRegisterDTO) : object|array|bool {

        $assert = new Assert\Collection(fields: [
            'name' =>  [
                new Assert\NotBlank(message: 'Name field is required and cannot be blank'),
                new Assert\Type('string')
            ],
            'email' =>  [
                new Assert\NotBlank(message: 'Email field is required and cannot be blank'),
                new Assert\Type('string'),
                new Assert\Email()
            ],
            'slug' =>  new Assert\NotBlank(message: 'Slug field is required and cannot be blank'),
            'domain' =>  new Assert\Optional(),
            'type' =>  [
                new Assert\NotBlank(message: 'Type field is required and cannot be blank'),
                new Assert\Type('int'),
            ],
            'tax_id' =>  [
                new Assert\NotBlank(message: 'Tax ID field is required and cannot be blank'),
                new Assert\Type('string'),
            ],
            'legal_name' =>  new Assert\NotBlank(message: 'Legal Name field is required and cannot be blank'),
            'trade_name' =>  new Assert\NotBlank(message: 'Trade Name field is required and cannot be blank'),
            'municipal_registration' => new Assert\Optional() ,
            'state_registration' =>  new Assert\Optional(),
            'phone' =>  new Assert\NotBlank(message: 'Phone field is required and cannot be blank'),
            'address' =>  new Assert\Optional(),
            'description' =>  new Assert\Optional(),
            'website' =>  new Assert\Optional(),
            'modules' =>  [
                    new Assert\NotBlank(message: 'Modules field is required and cannot be blank'),
                    new Assert\Type('array')
            ],
            'subscriptionPlan' =>  [
                new Assert\NotBlank(message: 'Subscription Plan field is required and cannot be blank'),
                new Assert\Type('int'),
            ],
            'primaryColor' =>  new Assert\Optional(), 
            'accentColor' =>  new Assert\Optional(), 
            'backgroundColor' =>  new Assert\Optional(), 
            'textColor' =>  new Assert\Optional(), 
        ], allowMissingFields: false, allowExtraFields: true);

        $violations = $this->validator->validate((array) $tenantRegisterDTO, [$assert]);

        if ($violations->count() > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            // This should handle the notification to the user, using the session notification method
            throw new AppExceptionHandler(implode('##,##', $errors), 400, null);
        }

         $response = $this->transaction(function () use ($tenantRegisterDTO) {
            $result = $this->model->store($tenantRegisterDTO);

            if (!$result || !isset($result->insertID)) {
                throw new AppExceptionHandler('Failed to create tenant.');
            }
            $businessRegisterDTO = new BusinessRegistrationDTO(
                name: $tenantRegisterDTO->name,
                email: $tenantRegisterDTO->email,
                type: (int)$tenantRegisterDTO->type,
                tax_id: $tenantRegisterDTO->tax_id,
                legal_name: $tenantRegisterDTO->legal_name,
                trade_name: $tenantRegisterDTO->trade_name,
                municipal_registration: $tenantRegisterDTO->municipal_registration,
                state_registration: $tenantRegisterDTO->state_registration,
                phone: $tenantRegisterDTO->phone,
                description: $tenantRegisterDTO->description,
                website: $tenantRegisterDTO->website,
                tenant_id: (int)$result->insertID,
            );
            

            $businessResult = $this->businessModel->store($businessRegisterDTO);

            if (!$businessResult || !isset($businessResult->insertID)) {
                throw new AppExceptionHandler('Failed to create business.');
            }

            $businessBrandingDTO = new BusinessBrandingStoreDTO(business_id: (int)$businessResult->insertID, app_name: $tenantRegisterDTO->name, accentColor: $tenantRegisterDTO->accentColor, primaryColor: $tenantRegisterDTO->primaryColor, textColor: $tenantRegisterDTO->textColor, buttonStyle: $tenantRegisterDTO->buttonStyle, fontStyle: $tenantRegisterDTO->fontFamily, logo: null);
            $businessBrandResult = $this->businessBrandingModel->store($businessBrandingDTO);

            if(!$businessBrandResult || !isset($businessBrandResult->insertID)){
                throw new AppExceptionHandler('Failed to create business branding!');
            }
            $result->business_id = $businessResult->insertID;
            $result->business_branding_id = $businessBrandResult->insertID;
            return $result;
         });

         return $response === false ? null : $response;
    }


    public function getTenant(?string $uuid) : object|array|bool {
        $response = null;
        $response = $this->model->getTenant($uuid);
        // foreach($response[0] as $key => $value){
        //     if($key === 'modules' || $key === 'phone' || $key === 'categories'){
        //         $response[0]->$key = Utils::pgArrayToPhp($value ?? '');
        //     }
        // }
        return $response ?? object();
    }

}