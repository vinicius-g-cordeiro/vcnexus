<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);
namespace App\Modules\Users\DTOs;

use App\Shared\Domain\DataTransferObjectInterface;

final class UserResponse implements DataTransferObjectInterface
{
    public function __construct(public readonly string $username, public readonly int|string $id, public readonly string $email, public readonly string $phone, public readonly string $uuid, public readonly int|string $active, public ?string $password, public readonly mixed $blocked){}

    public function toArray(): array{ return get_object_vars($this); }

    public static function fromArray(array $data): self{ return new self($data['username'], $data['id'], $data['email'], $data['phone'], $data['uuid'], $data['active'], $data['password'], $data['blocked']); }
}