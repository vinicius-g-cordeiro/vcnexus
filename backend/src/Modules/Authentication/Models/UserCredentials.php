<?php
/**
 * GENERATED from App\Schemas\Authentication\UserCredentialsSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 24/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authentication\Models;

use App\Shared\Schema\BaseModel;

final class UserCredentials extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public string $email;
    public string $password;
    public ?int $blocked;
    public ?int $remember;
    public ?string $token;
    public ?string $token_expires_at;
    public ?string $token_created_at;
    public ?string $last_login_at;
    public ?string $last_login_at_local;
    public ?string $last_login_ip;
    public ?string $last_login_agent;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $deleted_at;
    public ?int $created_by;
    public ?int $updated_by;
    public ?int $deleted_by;
    public ?string $deleted_reason;
    public function __construct() {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self
    {
        $newObject = new static();
        $newObject->id = isset($data['id']) ? (int) $data['id'] : null;
        $newObject->uuid = isset($data['uuid']) ? $data['uuid'] : null;
        $newObject->active = isset($data['active']) ? (int) $data['active'] : null;
        $newObject->email = isset($data['email']) ? $data['email'] : null;
        $newObject->password = isset($data['password']) ? $data['password'] : null;
        $newObject->blocked = isset($data['blocked']) ? (int) $data['blocked'] : null;
        $newObject->remember = isset($data['remember']) ? (int) $data['remember'] : null;
        $newObject->token = isset($data['token']) ? $data['token'] : null;
        $newObject->token_expires_at = isset($data['token_expires_at']) ? $data['token_expires_at'] : null;
        $newObject->token_created_at = isset($data['token_created_at']) ? $data['token_created_at'] : null;
        $newObject->last_login_at = isset($data['last_login_at']) ? $data['last_login_at'] : null;
        $newObject->last_login_at_local = isset($data['last_login_at_local']) ? $data['last_login_at_local'] : null;
        $newObject->last_login_ip = isset($data['last_login_ip']) ? $data['last_login_ip'] : null;
        $newObject->last_login_agent = isset($data['last_login_agent']) ? $data['last_login_agent'] : null;
        $newObject->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $newObject->updated_at = isset($data['updated_at']) ? $data['updated_at'] : null;
        $newObject->deleted_at = isset($data['deleted_at']) ? $data['deleted_at'] : null;
        $newObject->created_by = isset($data['created_by']) ? (int) $data['created_by'] : null;
        $newObject->updated_by = isset($data['updated_by']) ? (int) $data['updated_by'] : null;
        $newObject->deleted_by = isset($data['deleted_by']) ? (int) $data['deleted_by'] : null;
        $newObject->deleted_reason = isset($data['deleted_reason']) ? $data['deleted_reason'] : null;
        return $newObject;
    }
}