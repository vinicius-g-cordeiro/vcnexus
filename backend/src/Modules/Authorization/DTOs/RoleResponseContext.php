<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authorization\DTOs;

use App\Shared\Domain\DataTransferObjectInterface;

final class RoleResponseContext implements DataTransferObjectInterface
{
    public function __construct(
        public readonly ?int $id, 
        public readonly ?string $uuid, 
        public readonly ?string $name, 
        public readonly ?string $slug,
        public readonly ?string $description,
        public readonly ?int $tenant_id,
        public readonly ?int $active, 
        public readonly ?string $created_at,
        public readonly ?string $organization_name
    ) {}

    public function toArray(): array { return get_object_vars($this); }

    public static function fromArray(array $data): self { 
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            uuid: isset($data['uuid']) ? $data['uuid'] : null,
            name: isset($data['name']) ? $data['name'] : null,
            slug: isset($data['slug']) ? $data['slug'] : null,
            description: isset($data['description']) ? $data['description'] : null,
            tenant_id: isset($data['tenant_id']) ? (int) $data['tenant_id'] : null,
            active: isset($data['active']) ? (int) $data['active'] : null,
            created_at: isset($data['created_at']) ? $data['created_at'] : null,
            organization_name: isset($data['organization_name']) ? $data['organization_name'] : null
        );
    }
}