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

final class Index {
    /** @var object{unique: bool, columns: array<string>, condition: array<string[column], string[value]>}  */
    /**
     * @param string $name - idx_usernames_user_id
     * @param bool $unique - true
     * @param string $references: 'users',
     * @param array $columns - ['id']
     * @param array $condition - {active : B\'1\'}
     */
    function __construct(public readonly string $name = 'idx_', public readonly bool $unique = false, public readonly string $references = '', public readonly array $columns = [], public readonly array $condition = []) {   
    }
}