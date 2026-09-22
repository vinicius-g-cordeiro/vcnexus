<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Infrastructure\Database\RLS;

use App\Shared\Helpers\Utils;


final class RoleContext{
    /**
     * Applies the role context to the given ADOdb connection.
     * @param \ADOConnection $connection 
     * @param array $roles - the role identifier
     * @return void 
     */
    public function apply(\ADOConnection $connection, array $roles): void {
        $connection->Execute("SELECT set_config(?, ?, ?)", ['app.roles', Utils::PhpArrayToPg($roles), $this->scopeToTransaction()]);
    }

    /**
     * Returns `true` for the `LOCAL` scoping mode, `false` for the `SESSION` mode.
     * @return bool
     */
    private function scopeToTransaction(): bool {
        return true;
    }
}