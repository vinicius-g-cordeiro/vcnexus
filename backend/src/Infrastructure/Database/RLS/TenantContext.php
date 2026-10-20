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


final class TenantContext{
    /**
     * Applies the tenant context to the given ADOdb connection.
     * @param \ADOConnection $connection 
     * @param string $tenant_id - the tenant identifier
     * @return void 
     */
    public function apply(\ADOConnection $connection, string $tenant_id): void {
        $connection->Execute("SELECT set_config(?, ?, ?)", ['app.tenant_id', $tenant_id, $this->scopeToTransaction()]);
    }

    /**
     * Returns `true` for the `LOCAL` scoping mode, `false` for the `SESSION` mode.
     * @return bool
     */
    private function scopeToTransaction(): bool {
        return true;
    }
}