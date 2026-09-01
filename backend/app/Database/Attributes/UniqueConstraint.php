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

use App\Database\Attributes\Constraint;
use Attribute;
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class UniqueConstraint extends Constraint
{
    /**
     * @param string $name - 'fk_users_tenant
     * @param array<string> $columns - ['id']
    * @example Unique Constraint:
        CONSTRAINT "uq_users_id_tenant"
        UNIQUE ("id", "tenant_id"),
     */
    function __construct(public readonly string $name = 'fk', public readonly array $columns = [])
    {

    }
}