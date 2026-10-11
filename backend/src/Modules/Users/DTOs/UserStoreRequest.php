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
    public readonly ?string $username,
    public readonly string $email,
    public readonly ?string $birthdate,
    public readonly ?string $locale,
    public readonly string $password,
    public readonly ?string $phone,
    public readonly ?int $marital_status_id,
    public readonly ?int $nationality_id,
    public readonly ?int $sexual_orientation_id,
    public readonly ?int $gender_id,
    public readonly ?int $ethnicity_id,
    public readonly ?int $religion_id,    
    public readonly ?int $tenant_id,
    public readonly ?int $created_by,
    public readonly ?string $socialname,
    public readonly ?array $roles,
    public readonly ?array $emails,
    public readonly ?array $phones,
    public readonly ?array $contacts,
    public readonly ?array $consents,
    public readonly ?array $addresses,
    public readonly ?array $permissions,
    public readonly ?array $educations,
    public readonly ?string $avatar,
  ) {

  }

  public function toArray(): array
  {
    return get_object_vars($this);
  }

  public static function fromArray(array $data): self
  {
    return new self(
      firstname: $data['firstname'],
      surname: $data['surname'] ?? null,
      lastname: $data['lastname'],
      username: $data['username'] ?? null,
      email: $data['email'],
      birthdate: $data['birthdate'] ?? null,
      locale: $data['locale'] ?? null,
      password: $data['password'],
      phone: $data['phone'] ?? null,
      socialname: $data['socialname'] ?? null,
      marital_status_id: isset($data['marital_status_id']) ? (int) $data['marital_status_id'] : null,
      nationality_id: isset($data['nationality_id']) ? (int) $data['nationality_id'] : null,
      sexual_orientation_id: isset($data['sexual_orientation_id']) ? (int) $data['sexual_orientation_id'] : null,
      gender_id: isset($data['gender_id']) ? (int) $data['gender_id'] : null,
      ethnicity_id: isset($data['ethnicity_id']) ? (int) $data['ethnicity_id'] : null,
      religion_id: isset($data['religion_id']) ? (int) $data['religion_id'] : null,
      tenant_id: isset($data['tenant_id']) ? (int) $data['tenant_id'] : null,
      created_by: isset($data['created_by']) ? (int) $data['created_by'] : null,
      roles: $data['roles'] ?? null,
      emails: $data['emails'] ?? null,
      phones: $data['phones'] ?? null,
      contacts: $data['contacts'] ?? null,
      consents: $data['consents'] ?? null,
      addresses: $data['addresses'] ?? null,
      permissions: $data['permissions'] ?? null,
      educations: $data['educations'] ?? $data['education'] ?? null,
      avatar: $data['avatar'] ?? null
    );
  }
}