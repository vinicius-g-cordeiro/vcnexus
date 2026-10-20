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

/**
 * #[Index('idx_usernames_user_id', true, 'users', ['id'], {active : B\'1\'})]
 * @param string $username = 'joe.user';
 * @param bool $unique = false;
 * @param string $references = 'users';
 * @param array $columns = ['id', 'tenant_id'];
 * @param array<string, mixed> $condition = ['active' => true];
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Index {
    function __construct(public readonly string $name, public readonly bool $unique = false, public readonly string $references = '', public readonly array $columns = [], public readonly array $condition = []){}
}