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

final class Column {
    public bool $modified = false;
    public mixed $value = null {
        set(mixed $val) {
            $this->value = $val;
            $this->modified = true;
        }
    }

    function __construct(public readonly string $name, public readonly string $type, public readonly mixed $default = null, public readonly bool $isNull = false, public readonly string $comment = '', public readonly string|object $index = '') { 
    }
}


