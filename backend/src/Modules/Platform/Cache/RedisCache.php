<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Platform\Cache;

use Redis;

class RedisCache
{
    private Redis $redis;
    private int $defaultTtl = 3600;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function remember(string $key, callable $resolver, ?int $ttl = null): mixed
    {
        $cached = $this->redis->get($key);

        if ($cached !== false) {
            return json_decode($cached, true);
        }

        $value = $resolver();

        $this->redis->setex($key, $ttl ?? $this->defaultTtl, json_encode($value));

        return $value;
    }

    public function forget(string $key): void
    {
        $this->redis->del($key);
    }

    public function forgetMany(array $keys): void
    {
        if (!empty($keys)) {
            $this->redis->del($keys);
        }
    }
}
