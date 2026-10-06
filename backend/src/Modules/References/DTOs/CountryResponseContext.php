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

final class CountryResponseContext implements DataTransferObjectInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $code,
        public readonly ?string $iso_alpha2,
        public readonly ?string $iso_alpha3
    ) {}

    public function toArray(): array { return get_object_vars($this); }

    public static function fromArray(array $data): self { 
        return new self(
            isset($data['id']) ? (int) $data['id'] : null,
            $data['name'],
            $data['code'],
            $data['iso_alpha2'],
            $data['iso_alpha3']
        );
    }
}