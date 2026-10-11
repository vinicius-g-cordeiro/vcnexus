<?php
/**
 * GENERATED from App\Schemas\Platform\Contacts\ContactsSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 11/10/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Platform\Contacts\Models;

use App\Shared\Schema\BaseModel;

final class Contacts extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $owner_type_id;
    public int $owner_id;
    public int $type_id;
    public string $value;
    public string $label;
    public int $primary_contact;
    public ?int $category_id;
    public ?string $person;
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
        $newObject->owner_type_id = isset($data['owner_type_id']) ? (int) $data['owner_type_id'] : null;
        $newObject->owner_id = isset($data['owner_id']) ? (int) $data['owner_id'] : null;
        $newObject->type_id = isset($data['type_id']) ? (int) $data['type_id'] : null;
        $newObject->value = isset($data['value']) ? $data['value'] : null;
        $newObject->label = isset($data['label']) ? $data['label'] : null;
        $newObject->primary_contact = isset($data['primary_contact']) ? (int) $data['primary_contact'] : null;
        $newObject->category_id = isset($data['category_id']) ? (int) $data['category_id'] : null;
        $newObject->person = isset($data['person']) ? $data['person'] : null;
        return $newObject;
    }
}