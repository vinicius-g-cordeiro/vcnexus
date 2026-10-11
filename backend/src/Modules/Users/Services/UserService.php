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
use App\Modules\Platform\Addresses\Models\Addresses;
use App\Modules\Platform\Contacts\Models\Contacts;
use App\Modules\Users\DTOs\UserCredentialsResponse;
use App\Modules\Users\Models\{UserProfile, UserAddress, UserConsents, UserContact, UserEducation, UserSensitive};
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
            $userCredentialsStoreRequest = UserCredentials::fromArray([...$userStoreRequest->toArray(), 'created_by' => $this->session->get('user')->id ?? 1]);
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
            
            // Roles
            array_map(function ($value) use ($userCredentials) {
                $userRolesStoreRequest = UserRole::fromArray(['user_id' => $userCredentials->id, 'role_id' => $value, 'created_by' => $this->session->get('user')->id ?? 1, 'created_at' => date('Y-m-d H:i:s')]);
                // Save all the roles for this specific user_credentials
                $savedRole = $this->userRolesRepository->store($userRolesStoreRequest);
                if ($savedRole === false) {
                    error_log(sprintf("%s - Could not store user role, role_id: %s, user_id: %s", __METHOD__, $value, $userCredentials->id));
                    throw new TransactionFailedException('Could not store user role', 409);
                }
            }, $userStoreRequest->roles);

            // Permissions
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
                $userProfileAddressStoreRequest = Addresses::fromArray(['owner_id' => $userCredentials->id, 'owner_type_id' => 1, ...$value]);
                // Save all the addresses for this specific user_credentials
                $savedAddress = $this->userProfileRepository->store($userProfileAddressStoreRequest, ['id', 'uuid'], 'addresses');
                if ($savedAddress === false) {
                    error_log(sprintf("%s - Could not store user address, address_id: %s, user_id: %s", __METHOD__, $value, $userCredentials->id));
                    throw new TransactionFailedException('Could not store user address', 409);
                }
            }, $userStoreRequest->addresses);

            // Contact info -- phones
            array_map(function ($value) use ($userCredentials) {
                $userProfilePhoneStoreRequest = Contacts::fromArray(['owner_id' => $userCredentials->id, 'owner_type_id' => 1, ...$value]);

                // Save all the phones for this specific user_credentials
                $savedPhone = $this->userProfileRepository->store($userProfilePhoneStoreRequest, ['id', 'uuid'], 'contacts');
                if ($savedPhone === false) {
                    throw new TransactionFailedException('Could not store user phone', 409);
                }
            }, $userStoreRequest->phones);

            // Contact info -- emails
            array_map(function ($value) use ($userCredentials) {
                $userProfileEmailStoreRequest = Contacts::fromArray(['owner_id' => $userCredentials->id, 'owner_type_id' => 1, ...$value]);
                // Save all the emails for this specific user_credentials
                $savedEmail = $this->userProfileRepository->store($userProfileEmailStoreRequest, ['id', 'uuid'], 'contacts');
                if ($savedEmail === false) {
                    throw new TransactionFailedException('Could not store user email', 409);
                }
            }, $userStoreRequest->emails);

            // Educational info
            array_map(function($value) use ($userCredentials) {

                $userProfileEducationStoreRequest = UserEducation::fromArray(['user_id' => $userCredentials->id, ...$value]);
                // Save all the educations for this specific user_credentials
                $savedEducation = $this->userProfileRepository->store($userProfileEducationStoreRequest, ['id', 'uuid'], 'user_education');
                if ($savedEducation === false) {
                    throw new TransactionFailedException('Could not store user education', 409);
                }

            }, $userStoreRequest->educations ?? []);


            // consents
            array_map(function ($value) use ($userCredentials) {
                $userProfileConsentStoreRequest = UserConsents::fromArray(['user_id' => $userCredentials->id, ...$value]);
                // Save all the consents for this specific user_credentials
                $savedConsent = $this->userProfileRepository->store($userProfileConsentStoreRequest, ['id', 'uuid'], 'user_consents');
                if ($savedConsent === false) {
                    throw new TransactionFailedException('Could not store user consent', 409);
                }
            }, $userStoreRequest->consents);

            // sensitive information
            $userProfileSensitiveStoreRequest = UserSensitive::fromArray(['user_id' => $userCredentials->id, ...$userStoreRequest->toArray()]);
            // Save all the sensitive information for this specific user_credentials
            $savedSensitive = $this->userProfileRepository->store($userProfileSensitiveStoreRequest, ['id', 'uuid'], 'user_sensitive');
            if ($savedSensitive === false) {
                throw new TransactionFailedException('Could not store user sensitive information', 409);
            }
            
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

    public function profile(string $uuid): ?UserProfile
    {
        $user = $this->userProfileRepository->profile($uuid);
        return $user;
    }

    public function credentials(string $uuid): ?UserCredentialsResponse
    {
        $user = $this->userProfileRepository->credentials($uuid);
        return $user;
    }

    public function addresses(string $uuid): ?array
    {
        $user = $this->userProfileRepository->addresses($uuid);
        return $user;
    }

    public function consents(string $uuid): ?array
    {
        $user = $this->userProfileRepository->consents($uuid);
        return $user;
    }

    public function sensitive(string $uuid): ?UserSensitive
    {
        $user = $this->userProfileRepository->sensitive($uuid);
        return $user;
    }

    public function education(string $uuid): ?array
    {
        $user_education = $this->userProfileRepository->education($uuid);
        return $user_education;
    }

    public function contacts(string $uuid): ?array
    {
        $user = $this->userProfileRepository->contacts($uuid);
        return $user;
    }


}
