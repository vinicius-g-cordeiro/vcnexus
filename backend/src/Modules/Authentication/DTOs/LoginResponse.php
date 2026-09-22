<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authentication\DTOs;

use App\Shared\Domain\DataTransferObjectInterface;

final class LoginResponse implements DataTransferObjectInterface
{
    public function __construct(public readonly string $uuid, public readonly string $id, public readonly array $tenants, public readonly ?array $roles, public readonly ?array $permissions){}

    public function toArray(): array{ return get_object_vars($this); }

    public static function fromArray(array $data): self{ return new self($data['uuid'], $data['id'], $data['tenants'], $data['roles'], $data['permissions']); }
}