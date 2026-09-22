<?php
/**
 * GENERATED from App\Schemas\Authorization\UserPermissionSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 22/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authorization\Models;

use App\Shared\Schema\BaseModel;

final class UserPermission extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $user_id;
    public int $permission_id;
    public ?string $created_at;
    public ?string $updated_at;
    public ?int $created_by;
    public ?int $updated_by;
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
        $newObject->user_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $newObject->permission_id = isset($data['permission_id']) ? (int) $data['permission_id'] : null;
        $newObject->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $newObject->updated_at = isset($data['updated_at']) ? $data['updated_at'] : null;
        $newObject->created_by = isset($data['created_by']) ? (int) $data['created_by'] : null;
        $newObject->updated_by = isset($data['updated_by']) ? (int) $data['updated_by'] : null;
        return $newObject;
    }
}