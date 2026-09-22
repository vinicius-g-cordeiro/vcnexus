<?php
/**
 * GENERATED from App\Schemas\Users\UserSensitiveSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 22/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Users\Models;

use App\Shared\Schema\BaseModel;

final class UserSensitive extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $user_id;
    public ?string $socialname;
    public ?int $gender_id;
    public ?int $religion_id;
    public ?int $ethnicity_id;
    public ?int $sexual_orientation;
    public ?int $disability_id;
    public ?string $updated_at;
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
        $newObject->socialname = isset($data['socialname']) ? $data['socialname'] : null;
        $newObject->gender_id = isset($data['gender_id']) ? (int) $data['gender_id'] : null;
        $newObject->religion_id = isset($data['religion_id']) ? (int) $data['religion_id'] : null;
        $newObject->ethnicity_id = isset($data['ethnicity_id']) ? (int) $data['ethnicity_id'] : null;
        $newObject->sexual_orientation = isset($data['sexual_orientation']) ? (int) $data['sexual_orientation'] : null;
        $newObject->disability_id = isset($data['disability_id']) ? (int) $data['disability_id'] : null;
        $newObject->updated_at = isset($data['updated_at']) ? $data['updated_at'] : null;
        return $newObject;
    }
}