<?php
/**
 * GENERATED from App\Schemas\Users\UserEducationSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 11/10/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Users\Models;

use App\Shared\Schema\BaseModel;

final class UserEducation extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $user_id;
    public ?int $educational_level_id;
    public ?int $educational_type_id;
    public int $completion_status_id;
    public string $institution;
    public string $name;
    public string $start_date;
    public ?string $end_date;
    public ?string $expiration_date;
    public ?string $certification_number;
    public ?string $certification_url;
    public ?string $description;
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
        $newObject->educational_level_id = isset($data['educational_level_id']) ? (int) $data['educational_level_id'] : null;
        $newObject->educational_type_id = isset($data['educational_type_id']) ? (int) $data['educational_type_id'] : null;
        $newObject->completion_status_id = isset($data['completion_status_id']) ? (int) $data['completion_status_id'] : null;
        $newObject->institution = isset($data['institution']) ? $data['institution'] : null;
        $newObject->name = isset($data['name']) ? $data['name'] : null;
        $newObject->start_date = isset($data['start_date']) ? $data['start_date'] : null;
        $newObject->end_date = isset($data['end_date']) ? $data['end_date'] : null;
        $newObject->expiration_date = isset($data['expiration_date']) ? $data['expiration_date'] : null;
        $newObject->certification_number = isset($data['certification_number']) ? $data['certification_number'] : null;
        $newObject->certification_url = isset($data['certification_url']) ? $data['certification_url'] : null;
        $newObject->description = isset($data['description']) ? $data['description'] : null;
        return $newObject;
    }
}