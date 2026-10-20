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

use App\Modules\Authorization\Repositories\MenuRepository;
use App\Shared\Domain\BaseService;
use App\Infrastructure\Redis\RedisConnectionFactory;

final class MenuService extends BaseService
{

    private \Redis $redisConnection;
    public function __construct(\ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private MenuRepository $menuRepository, private RedisConnectionFactory $redisConnectionFactory) {
        parent::__construct($db, $tenant_id, $user_id, $roles);

        $this->redisConnection = $this->redisConnectionFactory->create();
    }

    public function index() {
        // Get menu mapping from Redis cache
        if($this->redisConnection->exists('menus')) {
            return json_decode($this->redisConnection->get('menus'), true);
        }

        // Get menu mapping from database if not exists
        $menus = $this->menuRepository->index();

        // Save menu mapping to Redis cache
        if($menus) {
            $this->redisConnection->set('menus', json_encode($menus));
        }

        return $menus;
    }
}