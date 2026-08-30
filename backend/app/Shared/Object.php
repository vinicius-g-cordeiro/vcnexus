<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

/**
 * Function to create a new object without having to create the \stdClass(),
 * It's a hacky way of doing it. 
 * example use: object(arg1: value1, arg2: value2);
 * @param array $arguments
 * @return object|null
 */
function object(...$arguments) : object {
    $object = new \stdClass();
    foreach ($arguments as $key => $value) {
        $object->{$key} = $value;
    }
    return $object;
}