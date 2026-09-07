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

#[Index(name: 'uq_tenant_business', unique: true, columns: ['tenant_id'], condition: ['active' => '1'], references:'business')]
final class BusinessSchema extends Schema
{
    public string $table = 'business';

    #[Column(type: 'BIGINT', default: 1, nullable: false, inherit: true)]
    public ?int $tenant_id = 1;

    #[Column(type: 'VARCHAR(100)', default: '', nullable: false, comment: '')]
    public ?string $legal_name = '';

    #[Column(type: 'VARCHAR(100)', default: null, nullable: true, comment: '')]
    public ?string $trade_name = '';

    #[Column(type: 'TEXT', default: null, nullable: true, comment: '')]
    public ?string $description = '';

    #[Column(type: 'SMALLINT', default: null, nullable: true, comment: 'Business type: MEI, LTDA, Simples Nacional, Simples Anonimo, CPF')]
    public ?int $type = 1;

    #[Column(type: 'VARCHAR(100)', default: '', nullable: false, comment: 'The legal tax identification of the business such as CNPJ on Brazil')]
    public ?string $tax_id = '';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: true, comment: 'Municipal registration license for operating the business')]
    public ?string $municipal_registration = '';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: true, comment: 'Municipal registration license for operating the business')]
    public ?string $state_registration = '';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: false, comment: '')]
    public ?string $email = '';

    #[Column(type: 'VARCHAR(100)', default: null, nullable: true, comment: '')]
    public ?string $website = '';
    
    #[Column(type: 'VARCHAR(20) ARRAY', default: 'ARRAY[]::VARCHAR(20)[]', nullable: true, comment: 'Array for phone numbers of the business')]
    public ?array $phone = [];

    #[Column(type: 'SMALLINT ARRAY', default: 'ARRAY[]::SMALLINT[]', nullable: true, comment: 'Array for business category')]
    public ?array $categories = [];

    public function __construct()
    {
        parent::__construct();
    }
}