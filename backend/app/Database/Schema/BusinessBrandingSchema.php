<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Database\Schema;

use App\Database\Attributes\Column;
use App\Database\Attributes\Index;

#[Index(name: 'uq_tenant_business', unique: true, columns: ['tenant_id'], condition: ['active' => '1'], references:'business_branding')]
#[Index(name: 'uq_business_businessBranding', unique: true, columns: ['business_id'], condition: ['active' => '1'], references:'business_branding')]
final class BusinessBrandingSchema extends Schema
{
    public string $table = 'business_branding';

    #[Column(type: 'BIGINT', default: 1, nullable: false, inherit: true)]
    public ?int $tenant_id = 1;

    #[Column(type: 'BIGINT', default: 1, nullable: false, inherit: true)]
    public ?int $business_id = 1;

    #[Column(type: 'VARCHAR(255)', default: '', nullable: true, comment: '')]
    public ?string $logo = '';

    #[Column(type: 'VARCHAR(255)', default: null, nullable: true, comment: '')]
    public ?string $app_name = '';

    #[Column(type: 'VARCHAR(100)', default: null, nullable: true, comment: '')]
    public ?string $primaryColor = '';
    
    #[Column(type: 'VARCHAR(100)', default: null, nullable: true, comment: '')]
    public ?string $accentColor = '';

    #[Column(type: 'VARCHAR(100)', default: null, nullable: true, comment: '')]
    public ?string $textColor = '';

    #[Column(type: 'VARCHAR(100)', default: null, nullable: true, comment: '')]
    public ?string $backgroundColor = '';

    #[Column(type: 'VARCHAR(100)', default: null, nullable: true, comment: '')]
    public ?string $fontStyle = '';

    #[Column(type: 'VARCHAR(100)', default: null, nullable: true, comment: '')]
    public ?string $buttonStyle = '';

    public function __construct()
    {
        parent::__construct();
    }
}