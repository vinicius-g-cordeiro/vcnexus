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

use App\DTOs\Authentication\AuthLoginDTO;
use App\DTOs\Authentication\LogoutDTO;
use App\DTOs\Authentication\ProfileUpdateDTO;
use App\DTOs\Authentication\AuthUserRegistrationDTO;
use App\DTOs\Authentication\TenantUserStoreDTO;
use App\DTOs\Users\UsernameRegistrationDTO;
use App\Exceptions\AppExceptionHandler;
use App\Model\Tenants\TenantUserModel;
use App\Model\UserModel;
use App\Model\UsernameModel;
use App\Service\Service;
use ADOConnection;
use App\Model\Model;
use App\Shared\Session;
use DateTimeZone;
use RuntimeException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class AuthService extends Service
{
    /**
     * @var UserModel
     */
    protected ?Model $model = null;
    function __construct(protected ?ADOConnection $connection = null)
    {
        parent::__construct($connection, new UserModel($connection), Session::getInstance());

    }


    function store(AuthUserRegistrationDTO $userRegistrationDTO): object|null
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

            $result = $this->model->store($userRegistrationDTO);
            if (!$result || !isset($result->insertID)) {
                throw new AppExceptionHandler('Failed to create user.');
            }

            $tenantUserModel = new TenantUserModel($this->connection);

            $tUserDTO = object(...$userRegistrationDTO, user_id: $result->insertID);

            dump($tUserDTO);
            
            $tenantUserDTO = new TenantUserStoreDTO(
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
                user_id: $result->insertID
            );
            
            $resultTenantUser = $tenantUserModel->store($tenantUserDTO);
            dd($resultTenantUser);

            if (!$result || !isset($result->insertID)) {
                throw new AppExceptionHandler('Failed to create tenant user.');
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

    public function login(AuthLoginDTO $authLoginDTO): object|null
    {
        $response = null;


        // Validate 
        $assert = new Assert\Collection(fields: [
            'login' => new Assert\NotBlank(message: 'Login is required!'),
            'password' => new Assert\NotBlank(message: 'Password is required and cannot be blank'),
        ], allowMissingFields: false, allowExtraFields: true);

        $violations = $this->validator->validate((array) $authLoginDTO, [$assert]);

        if ($violations->count() > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            // This should handle the notification to the user, using the session notification method
            throw new AppExceptionHandler(implode('##,##', $errors), 400, null);
        }

        $response = $this->transaction(function () use ($authLoginDTO) {
            
            $result = $this->model->login($authLoginDTO);

            if(isset($result->id) === false){
                throw new AppExceptionHandler('Couldn\'t login, try again later! If the problem persists, get in contact with the system administrator',500);
            }
            $date = new \DateTime('now', new DateTimeZone('UTC'));

            $dateLocal = $date->setTimezone(new DateTimeZone('America/Sao_Paulo'))->getTimestamp();
            $res = $this->model->update(new AuthLoginDTO(login: $result->email, id: (int) $result->id, last_login: (string) $date->getTimestamp(), last_login_local: (string) $dateLocal, last_ip: $_SERVER['REMOTE_ADDR'], last_agent: $_SERVER['HTTP_USER_AGENT']), ('id = ' . $result->id), bUpdate: false);
            $result->last_login = $dateLocal;
            return $result;
        });

        $this->session->set('user', $response === false ?: $response);
        
        return $response === false ? null : $response;
    }


    function updateProfile(ProfileUpdateDTO $profileUpdateDTO): object|null
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
            'password_confirmation' => new Assert\Callback(function ($value, ExecutionContextInterface $context) use ($profileUpdateDTO) {
                if ($profileUpdateDTO->password !== $profileUpdateDTO->password_confirmation) {
                    $context->buildViolation('Passwords does not match')->atPath('password_confirmation')->addViolation();
                }
            })
        ], allowMissingFields: false, allowExtraFields: true);

        $violations = $this->validator->validate((array) $profileUpdateDTO, [$assert]);

        if ($violations->count() > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            // This should handle the notification to the user, using the session notification method
            throw new AppExceptionHandler(implode('##,##', $errors), 400, null);
        }

        $response = $this->transaction(function () use ($profileUpdateDTO) {
            
            
            $result = $this->model->update($profileUpdateDTO, 'uuid = \'' . $profileUpdateDTO->uuid. '\'');
            
            if (!$result || !isset($result)) {
                throw new AppExceptionHandler('Failed to update user.');
            }

            $usernameModel = new UsernameModel($this->connection);

            $usernameResult = $usernameModel->update(
                new UsernameRegistrationDTO(
                    $profileUpdateDTO->username,
                    (string)$profileUpdateDTO->id,
                    (string)$profileUpdateDTO->tenant_id
                )
            , 'user_id = \'' . $profileUpdateDTO->id . '\'');

            if ($usernameResult === false || !(isset($usernameResult))) {
                throw new AppExceptionHandler('Failed to create username.');
            }

            return $profileUpdateDTO;
        });
        return $response === false ? null : $response;
    }



    public function getSelf(): object|null
    {
        $uuid = $this->session->get('user')->uuid;
        $response = $this->model->find($uuid, ['u.id', 'u.avatar', 'u.role' , 'u.roles', 'u.permissions', 'un.username', 'u.name', 'u.birthdate', 'u.phone', 'u.locale', 'b.legal_name as "organization_name"', 'u.gender', 'u.marital_status', 'u.religion', 'u.sexual_orientation' , 'b.tax_id', 'u.tenant_id', 'u.uuid', 'u.lastname', 'u.surname', 'u.email']);
        if ($response === false || $response == null || $response == object()) {
            throw new RuntimeException('404 - user not found', 404);
        }

        return $response === false ? null : $response;
    }

    public function getUser(?string $uuid = null): object|null
    {
        $response = $this->model->find($uuid, ['u.id', 'u.avatar', 'u.role', 'un.username', 'u.name', 'u.birthdate', 'u.phone', 'u.locale', 'b.legal_name as "organization_name"', 'u.gender', 'u.marital_status', 'u.religion', 'u.sexual_orientation' , 'b.tax_id', 'u.tenant_id', 'u.uuid', 'u.lastname', 'u.surname', 'u.email']);
        return $response === false ? null : $response;
    }

    public function logout(?string $uuid): object|null
    {
        $response = null;

        $userFound = $this->model->find($uuid, ['u.id', 'u.uuid']);
        if ($userFound === false) {
            throw new AppExceptionHandler(message: 'Could not find the user to logout', code: 400);
        }

        $response = $this->transaction(function () use ($userFound) {
            $result = $this->model->update(dataTransferObject: new LogoutDTO(id: (int) $userFound->id, uuid: $userFound->uuid), where: 'uuid = \'' . $userFound->uuid . '\'', bUpdate: false);
            return $result;
        });

        if (isset($response) && $response === 1) {
            $this->session->set('user', null);
        }

        return $response === 1 ? object(loggedOut: true) : null;
    }
}
