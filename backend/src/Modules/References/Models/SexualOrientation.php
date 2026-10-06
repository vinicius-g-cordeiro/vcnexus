<?php
/**
 * GENERATED from App\Schemas\References\SexualOrientationSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 06/10/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\References\Models;

use App\Shared\Schema\BaseModel;

final class SexualOrientation extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public string $name;
    public ?string $description;
    public string $label;
    public ?string $icon;
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
        $newObject->label = isset($data['label']) ? $data['label'] : null;
        $newObject->icon = isset($data['icon']) ? $data['icon'] : null;
        return $newObject;
    }
}