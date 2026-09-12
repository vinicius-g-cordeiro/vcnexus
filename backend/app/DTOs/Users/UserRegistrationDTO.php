<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\DTOs\Users;

use App\DTOs\DTOInterface;

final readonly class UserRegistrationDTO implements DTOInterface
{


    public function __construct(
        public readonly string $name,
        public readonly string $username,
        public readonly string $email,
        public readonly string $password,
        public readonly string $password_confirmation,
        public readonly ?string $surname = '',
        public readonly ?string $lastname = '',
        public readonly ?string $birthdate = '',
        public readonly ?int $gender = null,
        public readonly ?int $sexual_orientation = null,
        public readonly ?int $marital_status = null,
        public readonly ?int $religion = null, 
        public readonly ?string $locale = '',
        public readonly ?string $phone = '',
        public readonly ?string $bio = '',
        public readonly ?string $address = '',
        public readonly ?array $nickname = null,
        public readonly ?int $created_by = 1,
        public readonly ?int $tenant_id = null,
        public readonly ?array $permissions = null,
        public readonly ?array $roles = null,
    ) {
    }


}
