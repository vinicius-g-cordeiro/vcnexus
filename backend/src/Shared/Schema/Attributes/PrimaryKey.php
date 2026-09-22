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
 * #[Primary Key]
 * public string $id = '1';
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class PrimaryKey {
    public function __construct(public readonly bool $primaryKey = true, public readonly string $key = 'id') {}
}