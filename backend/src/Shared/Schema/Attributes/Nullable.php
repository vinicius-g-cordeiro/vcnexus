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
 * #[Nullable(nullable: true)]
 * public string $username = 'joe.user';
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Nullable {
    public bool $nullable = true;

    public function __construct(bool $nullable = true) {
        $this->nullable = $nullable;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }
}

