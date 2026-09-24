<?php
/**
 * GENERATED from App\Schemas\Authorization\RolePermissionSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 24/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authorization\Models;

use App\Shared\Schema\BaseModel;

final class RolePermission extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $role_id;
    public int $permission_id;
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
        $newObject->role_id = isset($data['role_id']) ? (int) $data['role_id'] : null;
        $newObject->permission_id = isset($data['permission_id']) ? (int) $data['permission_id'] : null;
        return $newObject;
    }
}