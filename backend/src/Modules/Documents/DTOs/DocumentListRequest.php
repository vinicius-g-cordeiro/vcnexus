<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Documents\DTOs;

use App\Shared\Domain\QueryObjectInterface;

final class DocumentListRequest implements QueryObjectInterface {

    public function __construct(public ?string $search, public ?int $active, public ?string $created_at,  public ?bool $paginate = true) {
    }

    public function toArray(): array {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self {
        return new self(
            search: $data['search'] ?? null,
            active: isset($data['active']) ? (int) $data['active'] : null,
            created_at: $data['created_at'] ?? null,
            paginate: isset($data['paginate']) ? (bool)$data['paginate'] : true);
    }
}
