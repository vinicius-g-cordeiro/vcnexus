<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\DTOs\Business\Branding;

use App\DTOs\DTOInterface;

final readonly class BusinessBrandingStoreDTO implements DTOInterface
{
    public function __construct(
        public readonly ?int $business_id,
        public readonly ?string $logo = null,
        public readonly ?string $app_name = null,
        public readonly ?string $primaryColor,
        public readonly ?string $accentColor = '',
        public readonly ?string $textColor = '',
        public readonly ?string $fontStyle = '',
        public readonly ?string $buttonStyle = '',
    ) {
    }
}