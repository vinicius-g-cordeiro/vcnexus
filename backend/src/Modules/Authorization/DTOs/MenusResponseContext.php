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

final class MenusResponseContext implements DataTransferObjectInterface
{
    public function __construct(
        public ?int $id = null,
        public ?string $uuid = null,
        public ?int $active = null,
        public ?int $parent_id = null,
        public string $label = '',
        public ?string $icon = null,
        public ?string $route = null,
        public int $order = 0,
        public ?array $permissions = null,
        public ?int $tenant_id = null,
        public ?array $children = null
    ) {

    }

    public function toArray(): array { return get_object_vars($this); }

    public static function fromArray(array $data): self { return new self(...$data); }
}