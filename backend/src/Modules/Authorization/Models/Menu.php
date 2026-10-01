<?php
/**
 * GENERATED from App\Schemas\Authorization\MenuSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 30/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authorization\Models;

use App\Shared\Schema\BaseModel;

final class Menu extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public ?int $parent_id;
    public string $label;
    public ?string $icon;
    public ?string $route;
    public int $order;
    public ?string $permissions;
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
        $newObject->parent_id = isset($data['parent_id']) ? (int) $data['parent_id'] : null;
        $newObject->label = isset($data['label']) ? $data['label'] : null;
        $newObject->icon = isset($data['icon']) ? $data['icon'] : null;
        $newObject->route = isset($data['route']) ? $data['route'] : null;
        $newObject->order = isset($data['order']) ? (int) $data['order'] : null;
        $newObject->permissions = isset($data['permissions']) ? $data['permissions'] : null;
        $newObject->tenant_id = isset($data['tenant_id']) ? (int) $data['tenant_id'] : null;
        return $newObject;
    }
}