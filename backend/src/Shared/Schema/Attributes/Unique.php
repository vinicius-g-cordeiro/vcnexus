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

use App\Shared\Schema\Attributes\Constraint;
use Attribute;

/**
 * #[Unique]
 * public string $username = 'joe.user';
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Unique extends Constraint { 

    function __construct(string $name = 'fk', public array $columns = [], public bool $deferred = false) {
        parent::__construct($name, $columns, $deferred);
    }
}