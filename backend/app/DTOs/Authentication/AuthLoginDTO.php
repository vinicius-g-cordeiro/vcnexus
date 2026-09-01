<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\DTOs\Authentication;

use App\DTOs\DTOInterface;

final readonly class AuthLoginDTO implements DTOInterface
{
    public function __construct(
        public readonly string $login,
        public readonly ?string $password = null,
        public readonly ?int $id = null,
        public readonly ?string $last_login = null,
        public readonly ?string $last_login_local = null,
        public readonly ?string $last_ip = null,
        public readonly ?string $last_agent = null,
    ) {
    }


}
