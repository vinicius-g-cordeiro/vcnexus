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

final class UserCredentialsResponse implements DataTransferObjectInterface {
    public function __construct(
        public readonly ?string $email,
        public readonly ?string $username,
        public readonly ?int $id,
        public readonly ?string $uuid,
        public readonly ?string $active,
        public readonly ?int $tenant_id
    ) {
    }

    public function toArray(): array {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self {
        return new self(
            email: $data['email'],
            username: $data['username'],
            id: isset($data['id']) ? (int) $data['id'] : null,
            uuid: isset($data['uuid']) ? $data['uuid'] : null,
            active: isset($data['active']) ? (int) $data['active'] : null,
            tenant_id: isset($data['tenant_id']) ? (int) $data['tenant_id'] : null,
        );
    }
}
