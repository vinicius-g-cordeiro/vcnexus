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

final class UsersListResponse implements DataTransferObjectInterface
{
    public function __construct(public readonly ?int $id, public readonly ?string $uuid, public readonly ?string $email, public readonly ?string $firstname,
     public readonly ?string $surname, public readonly ?string $lastname, public readonly ?string $birthdate, public readonly ?string $organization_name,
      public readonly ?string $created_at, public readonly ?array $roles, public readonly ?array $emails, public readonly ?array $phones, public readonly ?array $addresses){}

    public function toArray(): array{ return get_object_vars($this); }

    public static function fromArray(array $data): self{ return new self($data['id'] ?? null, $data['uuid'] ?? null, $data['email'] ?? null, $data['firstname'] ?? null,
     $data['surname'] ?? null, $data['lastname'] ?? null, $data['birthdate'] ?? null, $data['organization_name'] ?? null, $data['created_at'] ?? null, $data['roles'] ?? null,
      $data['emails'] ?? null, $data['phones'] ?? null, $data['addresses'] ?? null); }
}