<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Schemas\Tenant;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, Comment, ForeignKey, Index, Nullable, TenantScoped};

#[TenantScoped(tenant_id: 'tenant_id')]
#[Index(name: 'idx_tenant_business', unique: true, references: 'business', columns: ['tenant_id'], condition: ['active' => '1'])]
#[ForeignKey(name: 'fk_tenant', references: 'tenants', columns: ['id'], foreignKeys: ['tenant_id'], actionOnUpdate: true, deleteAction: 'CASCADE')]
final class BusinessSchema extends AbstractSchema
{
    public string $table = 'business';

    #[Column(type: 'varchar', length: 255)]
    public string $trade_name;

    #[Column(type: 'varchar', length: 255)]
    public string $fantasy_name;

    #[Column(type: 'varchar', length: 30)]
    public string $tax_id;

    #[Column(type: 'smallint', length: 1)]
    #[Nullable(nullable: true)]
    #[Comment('1: MEI(Micro Empreendedor Individual), 2: SLU(Sociedade Limitada Unipessoal), 3: EI(Empresa Individual), 4: LTDA(Sociedade Limitada), 5: SS(Sociedade Simples), 6: SA(Sociedade Anônima), etc..')]
    public string $type;

    #[Column(type: 'varchar', length: 30)]
    #[Nullable(nullable: true)]
    public string $state_registration;

    #[Column(type: 'varchar', length: 30)]
    #[Nullable(nullable: true)]
    public string $municipal_registration;

    #[Column(type: 'varchar', length: 500)]
    #[Nullable(nullable: true)]
    public string $description;

    #[Column(type: 'varchar', length: 100)]
    #[Nullable(nullable: true)]
    public string $website;

    
}
