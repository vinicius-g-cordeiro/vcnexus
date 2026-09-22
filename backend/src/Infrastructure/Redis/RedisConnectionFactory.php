<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Infrastructure\Redis;


class RedisConnectionFactory
{
    public function create() : \Redis {
        if(isset($this->redis)) {
            return $this->redis;
        }

        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), (int)getenv('REDIS_PORT'));
        $redis->auth($this->password());

        return $redis;
    }

    private function password() : string {
        return trim(file_get_contents(getenv('REDIS_PASSWORD')));
    }
}