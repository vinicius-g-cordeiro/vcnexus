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

final readonly class UsernameRegistrationDTO implements DTOInterface
{
    public function __construct(
        public readonly string $username,
        public readonly ?string $user_id,
        public readonly ?string $tenant_id,
    ) {
    }
}
