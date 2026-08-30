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

final class Constraint {
    /**
     * @param string $name - 'fk_users_tenant
     * @param array<string> $foreignKeys - ['tenant_id', 'user_id']
     * @param string $references - 'users'
     * @param array<string> $columns - ['id']
     * @param bool $actionOnUpdate - false
     * @param bool $actionOnDelete - true
     * @param string $deleteAction - CASCADE
     * @param bool $bIsUnique - true
     * @param array<string> $uniqueColumns ['id', 'tenant_id']
     * 
     * @example FKConstraint: 
        CONSTRAINT "fk_users_tenant"
        FOREIGN KEY ("tenant_id")
        REFERENCES "tenants" ("id")
        ON UPDATE NO ACTION
        ON DELETE CASCADE,
        
    * @example Unique Constraint:

        CONSTRAINT "uq_users_id_tenant"
        UNIQUE ("id", "tenant_id"),

     */
    function __construct(public readonly string $name = 'fk', public readonly array $foreignKeys = [], public readonly string $references = '',
                         public readonly array $columns = [], public readonly bool $actionOnUpdate = false,
                         public readonly bool $actionOnDelete = false, public readonly string $deleteAction = '',
                         public readonly bool $bIsUnique = false, public readonly array $uniqueColumns = []){

    }    
}