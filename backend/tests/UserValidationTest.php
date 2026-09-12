<?php
/**
 * @brief Verifies valid user registration data and the rules required by UserService.
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0
 * @date 2026/09/12
 */

declare(strict_types=1);

namespace Tests;

use App\DTOs\Users\UserRegistrationDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

require_once __DIR__ . '/../vendor/autoload.php';

final class UserValidationTest extends TestCase
{
    /** @brief Verifies that a complete user payload satisfies the registration contract. */
    public function testValidUserRegistration(): void
    {
        $user = new UserRegistrationDTO(
            name: 'Ada',
            surname: '',
            lastname: 'Lovelace',
            username: 'ada.lovelace',
            email: 'ada@example.test',
            password: 'A secure password 123!',
            password_confirmation: 'A secure password 123!',
            tenant_id: 10
        );

        $violations = Validation::createValidator()->validate((array)$user, new Assert\Collection([
            'name' => new Assert\NotBlank(),
            'lastname' => new Assert\NotBlank(),
            'username' => [new Assert\NotBlank(), new Assert\Length(min: 3, max: 100)],
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank(), new Assert\PasswordStrength()],
            'tenant_id' => [new Assert\NotBlank(), new Assert\Type('int')],
        ], allowMissingFields: false, allowExtraFields: true));

        self::assertCount(0, $violations);
        self::assertSame('ada.lovelace', $user->username);
        self::assertSame(10, $user->tenant_id);
    }

    /** @brief Verifies that malformed user data is rejected before persistence. */
    public function testInvalidUserRegistration(): void
    {
        $user = new UserRegistrationDTO(
            name: '',
            surname: '',
            lastname: '',
            username: 'a',
            email: 'not-an-email',
            password: '',
            password_confirmation: '',
            tenant_id: null
        );

        $violations = Validation::createValidator()->validate($user, new Assert\Collection([
            'name' => new Assert\NotBlank(),
            'lastname' => new Assert\NotBlank(),
            'username' => [new Assert\NotBlank(), new Assert\Length(min: 3)],
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => new Assert\NotBlank(),
            'tenant_id' => [new Assert\NotBlank(), new Assert\Type('int')],
        ], allowMissingFields: false, allowExtraFields: true));

        self::assertGreaterThan(0, $violations->count());
    }
}
