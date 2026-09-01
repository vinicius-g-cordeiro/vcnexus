<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Database\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
abstract class Constraint
{
    /**
     * @param string $name - 'fk_users_tenant
     * @param array<string> $columns - ['id']
     * @param bool $deferred - 
     * 
     * @example FKConstraint: 
        CONSTRAINT "fk_users_tenant"
        FOREIGN KEY ("tenant_id")
        REFERENCES "tenants" ("id")
        ON UPDATE NO ACTION
        ON DELETE CASCADE
        DEFERRED INITIALLY DEFERRED
     */
    function __construct(public readonly string $name = 'fk', public readonly array $columns = [], public readonly bool $deferred = false)
    {
    }
}