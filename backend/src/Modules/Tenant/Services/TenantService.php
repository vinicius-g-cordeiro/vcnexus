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

use App\Modules\Tenant\Models\{Business, BusinessBranding, Tenant};
use App\Shared\Domain\BaseService;
use App\Modules\Tenant\Repositories\{TenantRepository, BusinessRepository};
use App\Modules\Tenant\DTOs\{TenantListRequest, TenantListResponse, TenantStoreInRequest, TenantStoreRequest, TenantStoreResponse};
use App\Shared\Domain\Exceptions\TransactionFailedException;

final class TenantService extends BaseService
{
    public function __construct(\ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private TenantRepository $tenantRepository,private BusinessRepository $businessRepository) {
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
            $tenantStoreReq = Tenant::fromArray($tenantStoreRequest->toArray());
            
            $tenantRes = $this->tenantRepository->store($tenantStoreReq);

            if($tenantRes === false){
                throw new TransactionFailedException('Could not store tenant', 409);
            }

            $businessStoreReq = Business::fromArray(['tenant_id' => $tenantRes->id, ...$tenantStoreRequest->toArray()]);

            $businessRes = $this->businessRepository->store($businessStoreReq);

            if($businessRes === false){
                throw new TransactionFailedException('Could not store business', 409);
            }

            $businessBrandingRequest = BusinessBranding::fromArray(['business_id' => $businessRes->id, ...$tenantStoreRequest->toArray()]);

            $businessBrandingRes = $this->businessRepository->store($businessBrandingRequest, ['id', 'uuid'], 'business_brandings');

            if($businessBrandingRes === false){
                throw new TransactionFailedException('Could not store business branding', 409);
            }
            
            $tenantResponse = TenantStoreResponse::fromArray(['id' => $tenantRes->id, 'uuid' => $tenantRes->uuid]);

            return $tenantResponse;
        });

        return $tenant;
    }
}