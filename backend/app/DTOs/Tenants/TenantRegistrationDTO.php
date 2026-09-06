<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\DTOs\Tenants;

use App\DTOs\DTOInterface;

final readonly class TenantRegistrationDTO implements DTOInterface
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
        public readonly ?array $phone = [],
        public readonly ?string $website = '',
        public readonly ?string $address = '',
        public readonly ?string $domain = '',
        public readonly ?string $slug,
        public readonly ?string $primaryColor = '',
        public readonly ?string $accentColor = '',
        public readonly ?string $backgroundColor = '',
        public readonly ?string $textColor = '',
        public readonly ?string $fontFamily = '',
        public readonly ?string $buttonStyle = '',
        public readonly ?array $modules = [],
        public readonly ?int $subscriptionPlan = 1
    ) {
    }
}


// {
//   "legal_name": "Puro Sabor",
//   "trade_name": "Açai - Puro Sabor",
//   "type": "1",
//   "tax_id": "62.728.369/0001-72",
//   "municipal_registration": "",
//   "state_registration": "",
//   "email": "luizagoncalves618@gmail.com",
//   "phone": "+55 61 9 9345-6747",
//   "website": "https://purosabor.vcnexus.com",
//   "address": "R.15 QD 06 LT 24 APT 02 Res. Friburgo 24, Parque Nova Fribugo A, Cidade Ocidental - Goiás",
//   "domain": "purosabor.vcnexus.com",
//   "slug": "purosabor",
//   "bio": "",
//   "description": "Venda de açai, din-dins e outras gulozeimas",
//   "customization": {
//     "primary_color": "#10B981",
//     "accent_color": "#D4AF37",
//     "background_color": "#FFFFFF",
//     "text_color": "#171717",
//     "font_family": "merriweather",
//     "button_style": "rounded"
//   }
// }