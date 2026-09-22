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

final class UserStoreRequest implements DataTransferObjectInterface
{
  public function __construct(
    public readonly string $firstname,
    public readonly ?string $surname,
    public readonly string $lastname,
    public readonly string $username,
    public readonly string $email,
    public readonly ?string $birthdate,
    public readonly ?string $locale,
    public readonly string $password,
    public readonly ?string $phone,
    public readonly ?int $marital_status_id,
    public readonly ?int $nationality_id,
    public readonly ?int $tenant_id,
    public readonly ?int $created_by,
    public readonly ?array $roles,
    public readonly ?array $emails,
    public readonly ?array $phones,
    public readonly ?array $addresses,
    public readonly ?array $permissions
  ) {

  }

  public function toArray(): array
  {
    return get_object_vars($this);
  }

  public static function fromArray(array $data): self
  {
    return new self(
      $data['firstname'] ?? null,
      $data['surname'] ?? null,
      $data['lastname'] ?? null,
      $data['username'] ?? null,
      $data['email'] ?? null,
      $data['birthdate'] ?? null,
      $data['locale'] ?? null,
      $data['password'] ?? null,
      $data['phone'] ?? null,
      isset($data['marital_status_id']) ? (int) $data['marital_status_id'] : null,
      isset($data['nationality_id']) ? (int) $data['nationality_id'] : null,
      isset($data['tenant_id']) ? (int) $data['tenant_id'] : null,
      isset($data['created_by']) ? (int) $data['created_by'] : null,
      $data['roles'] ?? null,
      $data['emails'] ?? null,
      $data['phones'] ?? null,
      $data['addresses'] ?? null,
      $data['permissions'] ?? null
    );
  }
}