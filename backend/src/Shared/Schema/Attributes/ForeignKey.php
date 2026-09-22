<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Schema\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class ForeignKey extends Constraint
{
    /**
     * @param string $name - 'fk_users_tenant
     * @param array<string> $foreignKeys - ['tenant_id', 'user_id']
     * @param string $references - 'users'
     * @param array<string> $columns - ['id']
     * @param bool $actionOnUpdate - false = NO ACTION
     * @param string $deleteAction - ON DELETE CASCADE or SET NULL
     * 
     * @example FKConstraint: 
        CONSTRAINT "fk_users_tenant"
        FOREIGN KEY ("tenant_id")
        REFERENCES "tenants" ("id")
        ON UPDATE NO ACTION
        ON DELETE CASCADE,
     */
    function __construct(public string $name = 'fk', public array $foreignKeys = [], public string $references = '', public array $columns = [], public bool $actionOnUpdate = false, public string $deleteAction = '', public bool $deferred = true) {}
}