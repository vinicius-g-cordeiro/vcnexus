<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\DTOs\Business;

use App\DTOs\DTOInterface;

final readonly class BusinessRegistrationDTO implements DTOInterface
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $legal_name,
        public readonly ?string $trade_name = '',
        public readonly ?string $description = '',
        public readonly ?int $type = 1,
        public readonly ?string $tax_id,
        public readonly ?string $municipal_registration = '',
        public readonly ?string $state_registration = '',
        public readonly ?string $email = '',
        public readonly array $phone = [],
        public readonly ?string $website = '',
        public readonly ?int $tenant_id = 1,
    ) {
    }
}

