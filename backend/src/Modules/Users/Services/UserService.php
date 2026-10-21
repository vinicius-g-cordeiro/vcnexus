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

use ADOConnection;
use App\Shared\Http\Request;
use App\Shared\Http\Session;
use App\Shared\Helpers\Files;
use App\Shared\Domain\BaseService;
use App\Modules\Users\Cache\UserCache;
use App\Modules\Users\Models\UserProfile;
use App\Modules\Users\Models\UserConsents;
use App\Modules\Users\DTOs\UserListRequest;
use App\Modules\Users\Models\UserEducation;
use App\Modules\Users\Models\UserSensitive;
use App\Modules\Users\DTOs\UserStoreRequest;
use App\Modules\Users\DTOs\UsersListResponse;
use App\Modules\Users\DTOs\UserStoreResponse;
use App\Modules\Authorization\Models\UserRole;
use App\Modules\Platform\Contacts\Models\Contacts;
use App\Modules\Users\DTOs\UserCredentialsResponse;
use App\Modules\Authorization\Models\UserPermission;
use App\Modules\Platform\Addresses\Models\Addresses;
use App\Modules\Authentication\Models\UserCredentials;
use App\Modules\Authorization\Models\TenantMembership;
use App\Modules\Users\Repositories\UserProfileRepository;
use App\Shared\Domain\Exceptions\TransactionFailedException;
use App\Modules\Authorization\Repositories\UserRolesRepository;
use App\Modules\Users\Repositories\UserProfileConsentsRepository;
use App\Modules\Users\Repositories\UserTenantMembershipRepository;
use App\Modules\Authentication\Repositories\AuthenticationRepository;
use App\Modules\Authorization\Repositories\UserPermissionsRepository;

final class UserService extends BaseService
{
    public function __construct(ADOConnection $db, string $tenant_id, ?string $user_id, ?array $roles, private Request $request, private Session $session, private AuthenticationRepository $authenticationRepository, private UserProfileRepository $userProfileRepository, private UserProfileConsentsRepository $userProfileConsentsRepository, private UserTenantMembershipRepository $userTenantMembershipRepository, private UserRolesRepository $userRolesRepository, private UserPermissionsRepository $userPermissionsRepository, private UserCache $userCache) {
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

            $fileUtils = new Files();
            $avatarResponse = Files::upload_file($userStoreRequest->avatar, '/var/www/storage/upload/users/avatars/');
            $userProfileStoreRequest = UserProfile::fromArray(['user_id' => $userCredentials->id, ...$userStoreRequest->toArray(), 'avatar' => $avatarResponse['filename'] ?? null]);
            $user = $this->userProfileRepository->store($userProfileStoreRequest);
            if ($user === false) {
                if($avatarResponse['error'] !== ''){
                    Files::delete_file($avatarResponse['full_path']);
                }
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
                if($avatarResponse['error'] !== ''){
                    Files::delete_file($avatarResponse['full_path']);
                }
                throw new TransactionFailedException('Could not store user tenant membership', 409);
            }

            // Roles
            array_map(function ($value) use ($userCredentials, $fileUtils, $avatarResponse) {
                $userRolesStoreRequest = UserRole::fromArray(['user_id' => $userCredentials->id, 'role_id' => $value, 'created_by' => $this->session->get('user')->id ?? 1, 'created_at' => date('Y-m-d H:i:s')]);
                // Save all the roles for this specific user_credentials
                $savedRole = $this->userRolesRepository->store($userRolesStoreRequest);
                if ($savedRole === false) {
                    error_log(sprintf("%s - Could not store user role, role_id: %s, user_id: %s", __METHOD__, $value, $userCredentials->id));
                    if($avatarResponse['error'] !== ''){
                        Files::delete_file($avatarResponse['full_path']);
                    }
                    throw new TransactionFailedException('Could not store user role', 409);
                }
            }, $userStoreRequest->roles);

            // Permissions
            array_map(function ($value) use ($userCredentials, $fileUtils, $avatarResponse) {
                $userPermissionsStoreRequest = UserPermission::fromArray(['user_id' => $userCredentials->id, 'permission_id' => $value, 'created_by' => $this->session->get('user')->id ?? 1, 'created_at' => date('Y-m-d H:i:s')]);
                // Save all the permissions for this specific user_credentials
                $savedPermission = $this->userPermissionsRepository->store($userPermissionsStoreRequest);
                if ($savedPermission === false) {
                    error_log(sprintf("%s - Could not store user permission, permission_id: %s, user_id: %s", __METHOD__, $value, $userCredentials->id));
                    if($avatarResponse['error'] !== ''){
                        Files::delete_file($avatarResponse['full_path']);
                    }
                    throw new TransactionFailedException('Could not store user permission', 409);
                }
            }, $userStoreRequest->permissions);

            if(isset($userStoreRequest->addresses)){
                // Addresses
                array_map(function ($value) use ($userCredentials, $fileUtils, $avatarResponse) {
                    $userProfileAddressStoreRequest = Addresses::fromArray(['owner_id' => $userCredentials->id, 'owner_type_id' => 1, ...$value]);
                    // Save all the addresses for this specific user_credentials
                    $savedAddress = $this->userProfileRepository->store($userProfileAddressStoreRequest, ['id', 'uuid'], 'addresses');
                    if ($savedAddress === false) {
                        error_log(sprintf("%s - Could not store user address, address_id: %s, user_id: %s", __METHOD__, $value, $userCredentials->id));
                        if($avatarResponse['error'] !== ''){
                            Files::delete_file($avatarResponse['full_path']);
                        }
                        throw new TransactionFailedException('Could not store user address', 409);
                    }
                }, $userStoreRequest->addresses);
            }

            if(isset($userStoreRequest->phones)){
                // Contact info -- phones
                array_map(function ($value) use ($userCredentials, $fileUtils, $avatarResponse) {
                    $userProfilePhoneStoreRequest = Contacts::fromArray(['owner_id' => $userCredentials->id, 'owner_type_id' => 1, ...$value]);

                    // Save all the phones for this specific user_credentials
                    $savedPhone = $this->userProfileRepository->store($userProfilePhoneStoreRequest, ['id', 'uuid'], 'contacts');
                    if ($savedPhone === false) {
                        if($avatarResponse['error'] !== ''){
                            Files::delete_file($avatarResponse['full_path']);
                        }
                        throw new TransactionFailedException('Could not store user phone', 409);
                    }
                }, $userStoreRequest->phones);
            }

            if(isset($userStoreRequest->emails)){
                // Contact info -- emails
                array_map(function ($value) use ($userCredentials, $fileUtils, $avatarResponse) {
                    $userProfileEmailStoreRequest = Contacts::fromArray(['owner_id' => $userCredentials->id, 'owner_type_id' => 1, ...$value]);
                    // Save all the emails for this specific user_credentials
                    $savedEmail = $this->userProfileRepository->store($userProfileEmailStoreRequest, ['id', 'uuid'], 'contacts');
                    if ($savedEmail === false) {
                        if($avatarResponse['error'] !== ''){
                            Files::delete_file($avatarResponse['full_path']);
                        }
                        throw new TransactionFailedException('Could not store user email', 409);
                    }
                }, $userStoreRequest->emails);
            }

            if(isset($userStoreRequest->educations)){
                // Educational info
                array_map(function($value) use ($userCredentials, $fileUtils, $avatarResponse) {

                    $userProfileEducationStoreRequest = UserEducation::fromArray(['user_id' => $userCredentials->id, ...$value]);
                    // Save all the educations for this specific user_credentials
                    $savedEducation = $this->userProfileRepository->store($userProfileEducationStoreRequest, ['id', 'uuid'], 'user_education');
                    if ($savedEducation === false) {
                        if($avatarResponse['error'] !== ''){
                            Files::delete_file($avatarResponse['full_path']);
                        }
                        throw new TransactionFailedException('Could not store user education', 409);
                    }

                }, $userStoreRequest->educations ?? []);
            }

            if(isset($userStoreRequest->consents)){
                // consents
                array_map(function ($value) use ($userCredentials, $fileUtils, $avatarResponse) {
                    $userProfileConsentStoreRequest = UserConsents::fromArray(['user_id' => $userCredentials->id, ...$value]);
                    // Save all the consents for this specific user_credentials
                    $savedConsent = $this->userProfileRepository->store($userProfileConsentStoreRequest, ['id', 'uuid'], 'user_consents');
                    if ($savedConsent === false) {
                        if($avatarResponse['error'] !== ''){
                            Files::delete_file($avatarResponse['full_path']);
                        }
                        throw new TransactionFailedException('Could not store user consent', 409);
                    }
                }, $userStoreRequest->consents);
            }
            // sensitive information
            $userProfileSensitiveStoreRequest = UserSensitive::fromArray(['user_id' => $userCredentials->id, ...$userStoreRequest->toArray()]);
            // Save all the sensitive information for this specific user_credentials
            $savedSensitive = $this->userProfileRepository->store($userProfileSensitiveStoreRequest, ['id', 'uuid'], 'user_sensitive');
            if ($savedSensitive === false) {
                if($avatarResponse['error'] !== ''){
                    Files::delete_file($avatarResponse['full_path']);
                }
                throw new TransactionFailedException('Could not store user sensitive information', 409);
            }

            return $user;
        });

        if ($result === false) {
            return null;
        }

        return new UserStoreResponse(uuid: $result->uuid);
    }

    public function deactivate(string $uuid): bool
    {
        $destroyed = $this->transactional(function () use ($uuid) {
            $userProfile = $this->userProfileRepository->deactivate($uuid, 'user_credentials', true);
            if($userProfile === false){
                throw new TransactionFailedException('Could not deactivate user', 409);
            }

            $userProfile = $this->userProfileRepository->deactivate($uuid, 'user_profile', false);
            if($userProfile === false){
                throw new TransactionFailedException('Could not deactivate user', 409);
            }
            $userSensitive = $this->userProfileRepository->deactivate($uuid, 'user_sensitive', false);
            if($userSensitive === false){
                throw new TransactionFailedException('Could not deactivate user sensitive information', 409);
            }
            $userEducation = $this->userProfileRepository->deactivate($uuid, 'user_education', false);
            if($userEducation === false){
                throw new TransactionFailedException('Could not deactivate user education information', 409);
            }
            $userConsents = $this->userProfileRepository->deactivate($uuid, 'user_consents', false);
            if($userConsents === false){
                throw new TransactionFailedException('Could not deactivate user consents', 409);
            }
            $userAddresses = $this->userProfileRepository->deactivateAddresses($uuid);
            if($userAddresses === false){
                throw new TransactionFailedException('Could not deactivate user addresses', 409);
            }
            $userContacts = $this->userProfileRepository->deactivateContacts($uuid);
            if($userContacts === false){
                throw new TransactionFailedException('Could not deactivate user contacts', 409);
            }
            return true;
        });

        return $destroyed;
    }

    public function block(string $uuid): bool {
        $blocked = $this->transactional(function () use ($uuid) {
            $userProfile = $this->userProfileRepository->block($uuid, 'user_credentials', true);
            if($userProfile === false){
                return false;
            }
            return true;
        });

        return $blocked;
    }

    public function unblock(string $uuid): bool {
        $unblocked = $this->transactional(function () use ($uuid) {
            $userProfile = $this->userProfileRepository->unblock($uuid, 'user_credentials');
            if($userProfile === false){
                return false;
            }
            return true;
        });

        return $unblocked;
    }

    public function activate(string $uuid) : bool {
        $activated = $this->transactional(function () use ($uuid) {
            $userProfile = $this->userProfileRepository->activate($uuid, 'user_credentials');
            if($userProfile === false){
                return false;
            }

            return true;
        });

        return $activated;
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
