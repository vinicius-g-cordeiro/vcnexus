<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Service;

use App\DTOs\Authentication\TenantUserStoreDTO;
use App\DTOs\Users\AvatarStoreDTO;
use App\DTOs\Users\UserUpdateDTO;
use App\Model\Tenants\TenantUserModel;
use App\Model\UserModel;
use App\Service\Service;
use App\Shared\Connection;
use App\Model\Model;
use App\DTOs\Users\UsernameRegistrationDTO;
use App\Exceptions\AppExceptionHandler;
use App\Model\UsernameModel;
use App\DTOs\Users\UserRegistrationDTO;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UserService extends Service
{
    /**
     * @var TenantUserModel
     */
    protected ?Model $model = null;
    function __construct(protected ?Connection $connection = null)
    {
        parent::__construct($connection, new TenantUserModel($connection));
    }

    public function list(?object $parameters = null): object|array|bool
    {
        $response = null;
        $parameters ??= $this->request->params();

        $response = $this->model->list($parameters);
        return $response;
    }


    function store(UserRegistrationDTO $userRegistrationDTO): object|null
    {
        $response = null;

        // Validate 
        $assert = new Assert\Collection(fields: [
            'name' => new Assert\NotBlank(message: 'Name is required!'),
            'lastname' => new Assert\NotBlank(message: 'Last name is required!'),
            'surname' => new Assert\Optional(),
            'birthdate' => [
                new Assert\Optional(),
                new Assert\Date(),
            ],
            'gender' => [
                new Assert\Optional(),
                new Assert\Type('int'),
            ],
            'sexual_orientation' => [
                new Assert\Optional(),
                new Assert\Type('int'),
            ],
            'marital_status' => [
                new Assert\Optional(),
                new Assert\Type('int'),
            ],
            'locale' => [
                new Assert\Optional(),
                new Assert\Type('string', 'The type of localization should be passed as a string. Ex: \'pt-br\''),
            ],
            'username' => [
                new Assert\NotBlank(message: 'Username is required!'),
                new Assert\Length(min: 3, max: 100, charset: 'UTF-8'),
            ],
            'email' => [
                new Assert\NotBlank(message: 'Email is required'),
                new Assert\Email(message: 'Email should be a valid email')
            ],
            'password' => [
                new Assert\NotBlank(message: 'Password is required and cannot be blank'),
                new Assert\PasswordStrength()
            ],
            'tenant_id' => [
                new Assert\NotBlank(),
                new Assert\Type('int'),
            ],
            'password_confirmation' => new Assert\Callback(function ($value, ExecutionContextInterface $context) use ($userRegistrationDTO) {
                if ($userRegistrationDTO->password !== $userRegistrationDTO->password_confirmation) {
                    $context->buildViolation('Passwords does not match')->atPath('password_confirmation')->addViolation();
                }
            })
        ], allowMissingFields: false, allowExtraFields: true);

        $violations = $this->validator->validate((array) $userRegistrationDTO, [$assert]);

        if ($violations->count() > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            // This should handle the notification to the user, using the session notification method
            throw new AppExceptionHandler(implode('##,##', $errors), 400, null);
        }

        $response = $this->transaction(function () use ($userRegistrationDTO) {
            $userModel = new UserModel($this->connection);            

            $resultUser = $userModel->store($userRegistrationDTO);

            if (!$resultUser || !isset($resultUser->insertID)) {
                throw new AppExceptionHandler('Failed to create user.');
            }
            

            $userRegisterDTO = new TenantUserStoreDTO(
                name: $userRegistrationDTO->name,
                surname: $userRegistrationDTO->surname,
                lastname: $userRegistrationDTO->lastname,
                username: $userRegistrationDTO->username,
                email: $userRegistrationDTO->email,
                password: $userRegistrationDTO->password,
                password_confirmation: $userRegistrationDTO->password_confirmation,
                birthdate: $userRegistrationDTO->birthdate,
                gender: (int)($userRegistrationDTO->gender ?: null),
                sexual_orientation: (int)($userRegistrationDTO->sexual_orientation ?: null),
                marital_status: (int)($userRegistrationDTO->marital_status ?: null),
                locale: $userRegistrationDTO->locale ?: null,
                nickname: $userRegistrationDTO->nickname ?: null,
                created_by: (int)$this->session->get('user')->id ?? 1,
                user_id: $resultUser->insertID
            );

            $result = $this->model->store($userRegisterDTO);
            if (!$result || !isset($result->insertID)) {
                throw new AppExceptionHandler('Failed to create tenant\'s ser.');
            }

            $usernameModel = new UsernameModel($this->connection);

            $usernameResult = $usernameModel->store(
                new UsernameRegistrationDTO(
                    $userRegistrationDTO->username,
                    $result->insertID,
                    $result->tenant_id
                )
            );

            if ($usernameResult === false || !(isset($usernameResult))) {
                throw new AppExceptionHandler('Failed to create username.');
            }

            return $result;

        });
        return $response === false ? null : $response;
    }


    public function getUser(?string $uuid = null): object|null
    {
        $response = $this->model->find($uuid, ['u.id', 'u.avatar', 'u.role', 'u.roles' , 'u.permissions', 'un.username', 'u.name', 'u.birthdate', 'u.phone', 'u.locale', 'b.legal_name as "organization_name"', 'u.gender', 'u.marital_status', 'u.religion', 'u.sexual_orientation', 'b.tax_id', 'u.tenant_id as "tenant"', 'u.uuid', 'u.lastname', 'u.surname', 'u.email']);
        return ($response === false || isset($response->uuid) === false) ? null : $response;
    }


    function updateProfile(UserUpdateDTO $userUpdateDTO): object|null
    {
        $response = null;

        // Validate 
        $assert = new Assert\Collection(fields: [
            'name' => new Assert\NotBlank(message: 'Name is required!'),
            'lastname' => new Assert\NotBlank(message: 'Last name is required!'),
            'surname' => new Assert\Optional(),
            'birthdate' => [
                new Assert\Optional(),
                new Assert\Date(),
            ],
            'gender' => [
                new Assert\Optional(),
                new Assert\Type('int'),
            ],
            'sexual_orientation' => [
                new Assert\Optional(),
                new Assert\Type('int'),
            ],
            'marital_status' => [
                new Assert\Optional(),
                new Assert\Type('int'),
            ],
            'locale' => [
                new Assert\Optional(),
                new Assert\Type('string', 'The type of localization should be passed as a string. Ex: \'pt-br\''),
            ],
            'username' => [
                new Assert\NotBlank(message: 'Username is required!'),
                new Assert\Length(min: 3, max: 100, charset: 'UTF-8'),
            ],
            'email' => [
                new Assert\NotBlank(message: 'Email is required'),
                new Assert\Email(message: 'Email should be a valid email')
            ],
            'password' => [
                new Assert\Optional(),
                new Assert\PasswordStrength()
            ],
            'password_confirmation' => new Assert\Callback(function ($value, ExecutionContextInterface $context) use ($userUpdateDTO) {
                if ($userUpdateDTO->password !== $userUpdateDTO->password_confirmation) {
                    $context->buildViolation('Passwords does not match')->atPath('password_confirmation')->addViolation();
                }
            })
        ], allowMissingFields: false, allowExtraFields: true);

        $violations = $this->validator->validate((array) $userUpdateDTO, [$assert]);

        if ($violations->count() > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            // This should handle the notification to the user, using the session notification method
            throw new AppExceptionHandler(implode('##,##', $errors), 400, null);
        }

        $response = $this->transaction(function () use ($userUpdateDTO) {

            $result = $this->model->update($userUpdateDTO, 'uuid = \'' . $userUpdateDTO->uuid . '\'');

            if (!$result || !isset($result)) {
                throw new AppExceptionHandler('Failed to update user.');
            }

            $usernameModel = new UsernameModel($this->connection);

            $usernameResult = $usernameModel->update(
                new UsernameRegistrationDTO(
                    $userUpdateDTO->username,
                    (string) $result,
                    (string) $userUpdateDTO->tenant_id
                )
                ,
                'user_id = \'' . $result . '\''
            );

            if ($usernameResult === false || !(isset($usernameResult))) {
                throw new AppExceptionHandler('Failed to create username.');
            }

            return $userUpdateDTO;
        });
        return $response === false ? null : $response;
    }

    function deactivate(string $uuid): object|null
    {
        $response = null;

        $response = $this->transaction(function () use ($uuid) {

            $result = $this->model->deactivate('uuid = \''.$uuid.'\'');

            if (!$result || !isset($result)) {
                throw new AppExceptionHandler('Failed to deactivate user.');
            }

            $usernameModel = new UsernameModel($this->connection);

            $usernameResult = $usernameModel->deactivate('user_id = \'' . $result . '\'');

            if ($usernameResult === false || !(isset($usernameResult))) {
                throw new AppExceptionHandler('Failed to deactivate username.');
            }

            return object(deactivated: true);
        });
        return $response === false ? null : $response;
    }


    function activate(string $uuid): object|null
    {
        $response = null;

        $response = $this->transaction(function () use ($uuid) {

            $result = $this->model->activate('uuid = \''.$uuid.'\'');

            if (!$result || !isset($result)) {
                throw new AppExceptionHandler('Failed to activate user.');
            }

            $usernameModel = new UsernameModel($this->connection);

            $usernameResult = $usernameModel->activate('user_id = \'' . $result . '\'');

            if ($usernameResult === false || !(isset($usernameResult))) {
                throw new AppExceptionHandler('Failed to activate username.');
            }

            return object(deactivated: true);
        });
        return $response === false ? null : $response;
    }

    function block(string $uuid): object|null
    {
        $response = null;

        $response = $this->transaction(function () use ($uuid) {

            $result = $this->model->block('uuid = \''.$uuid.'\'');

            if (!$result || !isset($result)) {
                throw new AppExceptionHandler('Failed to block user.');
            }

            $usernameModel = new UsernameModel($this->connection);

            $usernameResult = $usernameModel->deactivate('user_id = \'' . $result . '\'');

            if ($usernameResult === false || !(isset($usernameResult))) {
                throw new AppExceptionHandler('Failed to block username.');
            }

            return object(deactivated: true);
        });
        return $response === false ? null : $response;
    }

    function unblock(string $uuid): object|null
    {
        $response = null;

        $response = $this->transaction(function () use ($uuid) {

            $result = $this->model->unblock('uuid = \''.$uuid.'\'');

            if (!$result || !isset($result)) {
                throw new AppExceptionHandler('Failed to block user.');
            }

            $usernameModel = new UsernameModel($this->connection);

            $usernameResult = $usernameModel->activate('user_id = \'' . $result . '\'');

            if ($usernameResult === false || !(isset($usernameResult))) {
                throw new AppExceptionHandler('Failed to block username.');
            }

            return object(deactivated: true);
        });
        return $response === false ? null : $response;
    }

    public function uploadAvatar(?AvatarStoreDTO $avatarStoreDTO) {
        $response = null;

        $response = $this->transaction(function () use ($avatarStoreDTO) {
            $result = $this->model->update($avatarStoreDTO, 'uuid = \''.$avatarStoreDTO->uuid.'\'');
            return $result;
        });
        return $response === false ? null : $response;
    }

}