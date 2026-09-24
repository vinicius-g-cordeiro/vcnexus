<?php
/**
 * GENERATED from App\Schemas\Users\UserProfileSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 24/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Users\Models;

use App\Shared\Schema\BaseModel;

final class UserProfile extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $user_id;
    public string $firstname;
    public ?string $surname;
    public string $lastname;
    public ?int $marital_status_id;
    public ?string $birthdate;
    public ?int $nationality_id;
    public ?string $locale;
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
        $newObject->firstname = isset($data['firstname']) ? $data['firstname'] : null;
        $newObject->surname = isset($data['surname']) ? $data['surname'] : null;
        $newObject->lastname = isset($data['lastname']) ? $data['lastname'] : null;
        $newObject->marital_status_id = isset($data['marital_status_id']) ? (int) $data['marital_status_id'] : null;
        $newObject->birthdate = isset($data['birthdate']) ? $data['birthdate'] : null;
        $newObject->nationality_id = isset($data['nationality_id']) ? (int) $data['nationality_id'] : null;
        $newObject->locale = isset($data['locale']) ? $data['locale'] : null;
        return $newObject;
    }
}