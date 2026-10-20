<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\RLS;

final class ExecutionContext
{
    private ?int $tenantId = null;
    private ?int $userId = null;

    /** @var array<int, string> */
    private array $roles = [];

    public function set(
        ?int $tenantId,
        ?int $userId,
        array $roles = []
    ): void {
        $this->tenantId = $tenantId;
        $this->userId = $userId;
        $this->roles = $roles;
    }

    public function tenantId(): ?int
    {
        return $this->tenantId;
    }

    public function userId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return array<int, string>
     */
    public function roles(): array
    {
        return $this->roles;
    }

    public function clear(): void
    {
        $this->tenantId = null;
        $this->userId = null;
        $this->roles = [];
    }
}