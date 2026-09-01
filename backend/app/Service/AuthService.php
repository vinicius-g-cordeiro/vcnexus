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

use App\Exceptions\AppExceptionHandler;
use App\Model\UserModel;
use App\Service\Service;
use App\Shared\Connection;
use App\Model\Model;
use App\Shared\Session;
use App\DTOs\Users\UserRegistrationDTO;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class AuthService extends Service
{
    function __construct(protected ?Connection $connection = null)
    {
        parent::__construct($connection, new UserModel($connection), Session::getInstance());

    }

    function store(UserRegistrationDTO $userRegistrationDTO): object|false|null
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
            return $this->model->store($userRegistrationDTO);
        });

        return $response;
    }
}
