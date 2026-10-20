<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\References\DTOs;

use App\Shared\Domain\DataTransferObjectInterface;

final class CityResponseContext implements DataTransferObjectInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $code,
        public readonly ?string $iso_alpha2,
        public readonly ?string $iso_alpha3,
        public readonly ?int $state_id, 
        public readonly ?int $geonames_id
    ) {}

    public function toArray(): array { return get_object_vars($this); }

    public static function fromArray(array $data): self { 
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            name: isset($data['name']) ? $data['name'] : null,
            code: isset($data['code']) ? $data['code'] : null,
            iso_alpha2: isset($data['iso_alpha2']) ? $data['iso_alpha2'] : null,
            iso_alpha3: isset($data['iso_alpha3']) ? $data['iso_alpha3'] : null,
            state_id: isset($data['state_id']) ? (int) $data['state_id'] : null,
            geonames_id: isset($data['geonames_id']) ? (int) $data['geonames_id'] : null
        );
    }
}