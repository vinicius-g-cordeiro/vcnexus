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

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class References extends Constraint
{
    /**
     * @param string $references - 'users'
     * @param array<string> $columns - ['id']
     * @param string $deleteAction - ON DELETE CASCADE or SET NULL
     * 
     * @example FKConstraint: 
        REFERENCES "tenants" ("id")
        ON DELETE CASCADE,
     */
    function __construct(public string $references = '', public array $columns = [], public string $deleteAction = '') {}
}