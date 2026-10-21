<?php

namespace App\Modules\Users\Cache;

use Redis;
use App\Modules\Users\Repositories\UserProfileRepository;

class UserCache
{
    private Redis $redis;
    private UserProfileRepository $repository;
    private int $ttl = 3600;

    public function __construct(Redis $redis, UserProfileRepository $repository)
    {
        $this->redis = $redis;
        $this->repository = $repository;
    }

    private function key(int $userId): string
    {
        return "user:{$userId}";
    }

    public function get(int $userId): array
    {
        $cached = $this->redis->get($this->key($userId));

        if ($cached !== false) {
            return json_decode($cached, true);
        }

        $user = $this->repository->findFullEntity($userId);

        $this->redis->setex($this->key($userId), $this->ttl, json_encode($user));

        return $user;
    }

    public function invalidate(int $userId): void
    {
        $this->redis->del($this->key($userId));
    }
}
