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

final class TenantSchema extends Schema
{
    public string $table = 'tenants';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: false, comment: '')]
    public ?string $name = '';

    #[Column(type: 'SMALLINT', default: '1', nullable: false, comment: 'Current status of the subscription plan. 1: active, 2: expired')]
    public ?int $status = 1;

    #[Column(type: 'VARCHAR(4) ARRAY', default: 'ARRAY[\'0\']::VARCHAR(4)[]', nullable: false, comment: 'Current status of the subscription plan. 1: active, 2: expired')]
    public ?array $modules = [];

    #[Column(type: 'SMALLINT', default: 1, nullable: false, comment: '')]
    public ?int $subscription_plan = 1;

    #[Column(type: 'varchar(255)', default: null, nullable: true)]
    public ?string $slug = '';

    #[Column(type: 'BIGINT', default: 1, nullable: false)]
    public ?int $business_id = 1;

    #[Column(type: 'BIGINT', default: 1, nullable: false, inherit: false)]
    public ?int $tenant_id = 1;

    public function __construct()
    {
        parent::__construct();
    }
}