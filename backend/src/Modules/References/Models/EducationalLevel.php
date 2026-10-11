<?php
/**
 * GENERATED from App\Schemas\References\EducationalLevelSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 11/10/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\References\Models;

use App\Shared\Schema\BaseModel;

final class EducationalLevel extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public string $name;
    public string $label;
    public ?string $description;
    public ?int $priority;
    public ?int $educational_type_id;
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
        $newObject->label = isset($data['label']) ? $data['label'] : null;
        $newObject->description = isset($data['description']) ? $data['description'] : null;
        $newObject->priority = isset($data['priority']) ? (int) $data['priority'] : null;
        $newObject->educational_type_id = isset($data['educational_type_id']) ? (int) $data['educational_type_id'] : null;
        return $newObject;
    }
}