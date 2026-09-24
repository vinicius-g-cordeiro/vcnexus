<?php
/**
 * GENERATED from App\Schemas\Authorization\RoleSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 24/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authorization\Models;

use App\Shared\Schema\BaseModel;

final class Role extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public string $name;
    public string $description;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $deleted_at;
    public ?int $created_by;
    public ?int $updated_by;
    public ?int $tenant_id;
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
        $newObject->name = isset($data['name']) ? $data['name'] : null;
        $newObject->description = isset($data['description']) ? $data['description'] : null;
        $newObject->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $newObject->updated_at = isset($data['updated_at']) ? $data['updated_at'] : null;
        $newObject->deleted_at = isset($data['deleted_at']) ? $data['deleted_at'] : null;
        $newObject->created_by = isset($data['created_by']) ? (int) $data['created_by'] : null;
        $newObject->updated_by = isset($data['updated_by']) ? (int) $data['updated_by'] : null;
        $newObject->tenant_id = isset($data['tenant_id']) ? (int) $data['tenant_id'] : null;
        return $newObject;
    }
}