<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Shared\Domain\DTOs;

use App\Shared\Domain\DataTransferObjectInterface;

final class StoreResponse implements DataTransferObjectInterface
{
    public function __construct(public readonly ?int $id, public readonly ?string $uuid, public readonly ?int $tenant_id) {}

    public function toArray(): array { return get_object_vars($this); }

    public static function fromArray(array $data): self { return new self($data['id'], $data['uuid'], $data['tenant_id']); }
}
