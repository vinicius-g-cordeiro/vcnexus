<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Tenant\DTOs;

use App\Shared\Domain\QueryObjectInterface;

final class TenantStoreResponse implements QueryObjectInterface
{

    public function __construct( public ?string $uuid , public ?string $id) {}

    public function toArray(): array{
        return get_object_vars($this);
    }

    public static function  fromArray(array $data): self{
        return new self(
            $data['uuid'],
            isset($data['id']) ? (string)$data['id'] : null
        );
    }
}