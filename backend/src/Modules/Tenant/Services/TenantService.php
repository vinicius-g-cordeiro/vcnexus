<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Tenant\Services;

use ADOConnection;
use App\Shared\Helpers\Files;
use App\Shared\Domain\BaseService;
use App\Modules\Tenant\Models\Tenant;
use App\Modules\Tenant\Models\Business;
use App\Modules\Tenant\DTOs\TenantListRequest;
use App\Modules\Tenant\DTOs\TenantListResponse;
use App\Modules\Tenant\Models\BusinessBranding;
use App\Modules\Tenant\DTOs\TenantStoreResponse;
use App\Modules\Tenant\DTOs\TenantStoreInRequest;
use App\Modules\Tenant\Repositories\TenantRepository;
use App\Modules\Tenant\Repositories\BusinessRepository;
use App\Shared\Domain\Exceptions\TransactionFailedException;

final class TenantService extends BaseService
{
    public function __construct(ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private TenantRepository $tenantRepository,private BusinessRepository $businessRepository) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    /**
     *
     * @param mixed $queryParameters
     * @return TenantListResponse[]|null
     */
    public function index(?TenantListRequest $queryParameters) : ?array {
        // TODO Validate inputs and sanitize

        $tenants = $this->tenantRepository->list($queryParameters);

        return $tenants;
    }

    public function store(?TenantStoreInRequest $tenantStoreRequest) : ?TenantStoreResponse {

        $tenant = $this->transactional(function() use ($tenantStoreRequest){
            $file = Files::upload_file($tenantStoreRequest->logo, '/var/www/storage/upload/tenants/logos/');

            $tenantStoreReq = Tenant::fromArray($tenantStoreRequest->toArray());

            $tenantRes = $this->tenantRepository->store($tenantStoreReq);

            if($tenantRes === false){
                // remove the uploaded file
                if(isset($tenantStoreRequest->logo) === true){
                    // delete the file from disk
                    @unlink($file['fullpath']);
                }
                throw new TransactionFailedException('Could not store tenant', 409);
            }

            $businessStoreReq = Business::fromArray(['tenant_id' => $tenantRes->id, ...$tenantStoreRequest->toArray()]);

            $businessRes = $this->businessRepository->store($businessStoreReq);

            if($businessRes === false){
                // remove the uploaded file
                if(isset($tenantStoreRequest->logo) === true){
                    // delete the file from disk
                    @unlink($file['fullpath']);
                }
                throw new TransactionFailedException('Could not store business', 409);
            }

            $businessBrandingRequest = BusinessBranding::fromArray(['business_id' => $businessRes->id, ...$tenantStoreRequest->toArray(), 'logo'=> $file['filename']]);

            $businessBrandingRes = $this->businessRepository->store($businessBrandingRequest, ['id', 'uuid'], 'business_brandings');

            if($businessBrandingRes === false){
                                // remove the uploaded file
                if(isset($tenantStoreRequest->logo) === true){
                    // delete the file from disk
                    @unlink($file['fullpath']);
                }
                throw new TransactionFailedException('Could not store business branding', 409);
            }

            $tenantResponse = TenantStoreResponse::fromArray(['id' => $tenantRes->id, 'uuid' => $tenantRes->uuid]);

            return $tenantResponse;
        });

        return $tenant;
    }

    public function deactivate(string $uuid): bool {
        return $this->tenantRepository->deactivate($uuid);
    }

    public function activate(string $uuid): bool {
        return $this->tenantRepository->activate($uuid);
    }

    public function organization(string $uuid): array {
        return $this->tenantRepository->organization($uuid);
    }

    public function brandings(string $uuid): array {
        return $this->tenantRepository->brandings($uuid);
    }

    public function addresses(string $uuid): array {
        return $this->tenantRepository->addresses($uuid);
    }

    public function contacts(string $uuid): array {
        return $this->tenantRepository->contacts($uuid);
    }

}
