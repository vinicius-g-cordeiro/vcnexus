<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authorization\Services;

use App\Modules\Authorization\Repositories\PermissionRepository;
use App\Shared\Domain\BaseService;
use App\Infrastructure\Redis\RedisConnectionFactory;

final class PermissionService extends BaseService
{

    private \Redis $redisConnection;
    public function __construct(\ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private PermissionRepository $permissionsRepository, private RedisConnectionFactory $redisConnectionFactory) {
        parent::__construct($db, $tenant_id, $user_id, $roles);

        $this->redisConnection = $this->redisConnectionFactory->create();
        
    }

    public function index() {
        // Get permissions mapping from Redis cache
        if($this->redisConnection->exists('permissions')) {
            return json_decode($this->redisConnection->get('permissions'), true);
        }

        // Get permissions mapping from database if not exists
        $permissions = $this->permissionsRepository->index();

        // Save permissions mapping to Redis cache
        if($permissions) {
            $this->redisConnection->set('permissions', json_encode($permissions));
        }

        return $permissions;
    }

    public function getUserPermissions(string $uuid) {
        return $this->permissionsRepository->getUserPermissions($uuid);
    }
}