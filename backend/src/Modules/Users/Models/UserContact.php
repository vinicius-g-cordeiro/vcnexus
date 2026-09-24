<?php
/**
 * GENERATED from App\Schemas\Users\UserContactSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 24/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Users\Models;

use App\Shared\Schema\BaseModel;

final class UserContact extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $user_id;
    public int $type;
    public string $value;
    public string $label;
    public int $primary_contact;
    public ?string $category;
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
        $newObject->user_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $newObject->type = isset($data['type']) ? (int) $data['type'] : null;
        $newObject->value = isset($data['value']) ? $data['value'] : null;
        $newObject->label = isset($data['label']) ? $data['label'] : null;
        $newObject->primary_contact = isset($data['primary_contact']) ? (int) $data['primary_contact'] : null;
        $newObject->category = isset($data['category']) ? $data['category'] : null;
        $newObject->person = isset($data['person']) ? $data['person'] : null;
        return $newObject;
    }
}