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


final class UserContext{
    /**
     * Applies the user context to the given ADOdb connection.
     * @param \ADOConnection $connection 
     * @param string $user_id - the user identifier
     * @return void 
     */
    public function apply(\ADOConnection $connection, string $user_id): void {
        $connection->Execute("SELECT set_config(?, ?, ?)", ['app.user_id', $user_id, $this->scopeToTransaction()]);
    }

    /**
     * Returns `true` for the `LOCAL` scoping mode, `false` for the `SESSION` mode.
     * @return bool
     */
    private function scopeToTransaction(): bool {
        return true;
    }
}