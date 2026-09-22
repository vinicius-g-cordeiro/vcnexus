<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Database\RLS\{RoleContext, TenantContext, UserContext};
use App\Infrastructure\Redis\RedisConnectionFactory;

final class ConnectionProvider {
    private ?\ADOConnection $connection = null;
    private ?\Redis $redis = null;

    public function __construct(private ConnectionFactory $factory, private TenantContext $tenantContext, private RoleContext $roleContext, private UserContext $userContext, private RedisConnectionFactory $redisFactory) {}

    public function get(?string $tenant_id = null, ?string $user_id = null, ?array $roles = null): \ADOConnection {
        if (isset($this->connection) === false) {
            $this->connection = $this->factory->create();
        }

        if (isset($this->redis) === false) {
            $this->redis = $this->redisFactory->create();
        }

        if ($tenant_id !== null) {
            $this->tenantContext->apply($this->connection, $tenant_id);
        }

        if ($user_id !== null) {
            $this->userContext->apply($this->connection, $user_id);
        }

        if ($roles !== null) {
            $this->roleContext->apply($this->connection, $roles);
        }

        return $this->connection;
    }

    public function getUnscoped(): \ADOConnection {
        if (isset($this->connection) === false) {
            $this->connection = $this->factory->create();
        }

        if (isset($this->redis) === false) {
            $this->redis = $this->redisFactory->create();
        }

        return $this->connection;
    }
}

