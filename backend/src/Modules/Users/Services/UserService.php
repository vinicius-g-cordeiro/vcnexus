<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Users\Services;

use App\Modules\Authentication\Models\UserCredentials;
use App\Modules\Authorization\Models\UserPermission;
use App\Modules\Users\Models\{UserProfile, UserAddress, UserContact};
use App\Modules\Authorization\Models\{TenantMembership, UserRole};
use App\Modules\Authorization\Repositories\{UserRolesRepository, UserPermissionsRepository};
use App\Modules\Authentication\Repositories\AuthenticationRepository;
use App\Modules\Users\DTOs\{UserListRequest, UsersListResponse};
use App\Modules\Users\Repositories\{UserProfileConsentsRepository, UserProfileRepository, UserTenantMembershipRepository};
use App\Shared\Domain\BaseService;
use App\Shared\Http\{Session, Request};
use App\Modules\Users\DTOs\{UserStoreRequest, UserStoreResponse};
use App\Shared\Domain\Exceptions\TransactionFailedException;

final class UserService extends BaseService
{
    public function __construct(
        \ADOConnection $db,
        string $tenant_id,
        ?string $user_id,
        ?array $roles,
        private Request $request,
        private Session $session,
        private AuthenticationRepository $authenticationRepository,
        private UserProfileRepository $userProfileRepository,
        private UserProfileConsentsRepository $userProfileConsentsRepository,
        private UserTenantMembershipRepository $userTenantMembershipRepository,
        private UserRolesRepository $userRolesRepository,
        private UserPermissionsRepository $userPermissionsRepository
    ) {
        parent::__construct($db, $tenant_id, $user_id, $roles);


    }

    /**
     *  Store a new user on user_credentials and user_profile tables, but do not store sensitive data, which should be done separately
     * @param UserStoreRequest $userStoreRequest - user data 
     * @return UserStoreResponse|null - user uuid 
     * @throws TransactionFailedException|TransactionFailedException|\RuntimeException
     */
    public function store(UserStoreRequest $userStoreRequest): ?UserStoreResponse
    {

        /// @TODO Validate user data before storing
        $result = $this->transactional(function () use ($userStoreRequest) {
            $userCredentialsStoreRequest = UserCredentials::fromArray(['created_by' => $this->session->get('user')->id ?? 1, ...$userStoreRequest->toArray()]);
            $userCredentials = $this->authenticationRepository->store($userCredentialsStoreRequest);
            
            
            if ($userCredentials === false) {
                throw new TransactionFailedException('Could not store user credentials', 409);
            }

            $userProfileStoreRequest = UserProfile::fromArray(['user_id' => $userCredentials->id, ...$userStoreRequest->toArray()]);
            $user = $this->userProfileRepository->store($userProfileStoreRequest);
            if ($user === false) {
                throw new TransactionFailedException('Could not store user profile');
            }

            $userTenantMembershipRequest = TenantMembership::fromArray([
                'user_id' => $userCredentials->id,
                'tenant_id' => $userStoreRequest->tenant_id ? $userStoreRequest->tenant_id : $this->tenant_id,
                'joined_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->session->get('user')->id ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'role_id' => $userStoreRequest->roles[0],
            ]);
            $userTenantMember = $this->userTenantMembershipRepository->store($userTenantMembershipRequest);

            if ($userTenantMember === false) {
                throw new TransactionFailedException('Could not store user tenant membership', 409);
            }
            array_map(function ($value) use ($userCredentials) {
                $userRolesStoreRequest = UserRole::fromArray(['user_id' => $userCredentials->id, 'role_id' => $value, 'created_by' => $this->session->get('user')->id ?? 1, 'created_at' => date('Y-m-d H:i:s')]);
                // Save all the roles for this specific user_credentials
                $savedRole = $this->userRolesRepository->store($userRolesStoreRequest);
                if ($savedRole === false) {
                    error_log(sprintf("%s - Could not store user role, role_id: %s, user_id: %s", __METHOD__, $value, $userCredentials->id));
                    throw new TransactionFailedException('Could not store user role', 409);
                }
            }, $userStoreRequest->roles);

            array_map(function ($value) use ($userCredentials) {
                $userPermissionsStoreRequest = UserPermission::fromArray(['user_id' => $userCredentials->id, 'permission_id' => $value, 'created_by' => $this->session->get('user')->id ?? 1, 'created_at' => date('Y-m-d H:i:s')]);
                // Save all the permissions for this specific user_credentials
                $savedPermission = $this->userPermissionsRepository->store($userPermissionsStoreRequest);
                if ($savedPermission === false) {
                    error_log(sprintf("%s - Could not store user permission, permission_id: %s, user_id: %s", __METHOD__, $value, $userCredentials->id));
                    throw new TransactionFailedException('Could not store user permission', 409);
                }
            }, $userStoreRequest->permissions);

            // Addresses

            array_map(function ($value) use ($userCredentials) {
                $userProfileAddressStoreRequest = UserAddress::fromArray(['user_id' => $userCredentials->id, ...$value]);
                // Save all the addresses for this specific user_credentials
                $savedAddress = $this->userProfileRepository->store($userProfileAddressStoreRequest, ['id', 'uuid'], 'user_address');
                if ($savedAddress === false) {
                    error_log(sprintf("%s - Could not store user address, address_id: %s, user_id: %s", __METHOD__, $value, $userCredentials->id));
                    throw new TransactionFailedException('Could not store user address', 409);
                }
            }, $userStoreRequest->addresses);

            // Contact info -- phone


            array_map(function ($value) use ($userCredentials) {
                $userProfilePhoneStoreRequest = UserContact::fromArray(['user_id' => $userCredentials->id, ...$value]);

                // Save all the phones for this specific user_credentials
                $savedPhone = $this->userProfileRepository->store($userProfilePhoneStoreRequest, ['id', 'uuid'], 'user_contact');
                if ($savedPhone === false) {
                    throw new TransactionFailedException('Could not store user phone', 409);
                }
            }, $userStoreRequest->phones);

            // Contact info -- email

            array_map(function ($value) use ($userCredentials) {
                $userProfileEmailStoreRequest = UserContact::fromArray(['user_id' => $userCredentials->id, ...$value]);
                // Save all the emails for this specific user_credentials
                $savedEmail = $this->userProfileRepository->store($userProfileEmailStoreRequest, ['id', 'uuid'], 'user_contact');
                if ($savedEmail === false) {
                    throw new TransactionFailedException('Could not store user email', 409);
                }
            }, $userStoreRequest->emails);


            return $user;
        });

        if ($result === false) {
            return null;
        }

        return new UserStoreResponse(uuid: $result->uuid);
    }

    /**
     * List all users
     * @param UserListRequest $queryParameters
     * @return array<UsersListResponse>|null
     */
    public function index(UserListRequest $queryParameters): ?array
    {
        $users = $this->userProfileRepository->list($queryParameters);
        return $users;
    }


}
