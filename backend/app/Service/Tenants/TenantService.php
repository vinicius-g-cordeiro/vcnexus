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

use App\DTOs\DTOInterface;
use App\DTOs\Business\BusinessRegistrationDTO;
use App\Exceptions\AppExceptionHandler;
use App\Model\Tenants\BusinessModel;
use App\Service\Service;
use App\Model\Tenants\TenantModel;
use App\Shared\Connection;
use App\Model\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class TenantService extends Service {
    protected ?BusinessModel $businessModel = null;
    function __construct(protected ?Connection $connection = null){
        parent::__construct($connection, new TenantModel($connection));

        $this->businessModel = new BusinessModel($this->connection);
    }

    public function list(?object $parameters = null) : object|array|bool {
        $response = null;

        $parameters ??= $this->request->params();
        
        $response = $this->model->list($parameters);
        return $response ?? object();
    }

    public function store(?DTOInterface $tenantRegisterDTO) : object|array|bool {

        $assert = new Assert\Collection(fields: [
            'name' =>  [
                new Assert\NotBlank(message: 'This field is required and cannot be blank'),
                new Assert\Type('string')
            ],
            'email' =>  [
                new Assert\NotBlank(message: 'This field is required and cannot be blank'),
                new Assert\Type('string'),
                new Assert\Email()
            ],
            'slug' =>  new Assert\NotBlank(message: 'This field is required and cannot be blank'),
            'domain' =>  new Assert\Optional(),
            'type' =>  [
                new Assert\NotBlank(message: 'This field is required and cannot be blank'),
                new Assert\Type('int'),
            ],
            'tax_id' =>  [
                new Assert\NotBlank(message: 'This field is required and cannot be blank'),
                new Assert\Type('string'),
            ],
            'legal_name' =>  new Assert\NotBlank(message: 'This field is required and cannot be blank'),
            'trade_name' =>  new Assert\NotBlank(message: 'This field is required and cannot be blank'),
            'municipal_registration' => new Assert\Optional() ,
            'state_registration' =>  new Assert\Optional(),
            'phone' =>  new Assert\NotBlank(message: 'This field is required and cannot be blank'),
            'address' =>  new Assert\Optional(),
            'description' =>  new Assert\Optional(),
            'website' =>  new Assert\Optional(),
            'modules' =>  [
                    new Assert\NotBlank(message: 'This field is required and cannot be blank'),
                    new Assert\Type('array')
            ],
            'subscriptionPlan' =>  [
                new Assert\NotBlank(message: 'This field is required and cannot be blank'),
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

            $result->business_id = $businessResult->insertID;
            return $result;
         });

         return $response === false ? null : $response;
    }

}