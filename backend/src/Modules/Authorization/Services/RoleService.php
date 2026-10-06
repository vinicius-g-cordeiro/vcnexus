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

use App\Modules\Authorization\Repositories\RoleRepository;
use App\Shared\Domain\BaseService;
use App\Infrastructure\Redis\RedisConnectionFactory;

final class RoleService extends BaseService
{

    private \Redis $redisConnection;
    public function __construct(\ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private RoleRepository $roleRepository, private RedisConnectionFactory $redisConnectionFactory) {
        parent::__construct($db, $tenant_id, $user_id, $roles);

        $this->redisConnection = $this->redisConnectionFactory->create();
        
    }

    public function index() : ?array {
        // Get role mapping from Redis cache
        if($this->redisConnection->exists('roles')) {
            return json_decode($this->redisConnection->get('roles'), true);
        }

        // Get roles mapping from database if not exists
        $roles = $this->roleRepository->index();

        // Save role mapping to Redis cache
        if($roles) {
            $this->redisConnection->set('roles', json_encode($roles));
        }

        return $roles;
    }
}