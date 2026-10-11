<?php
/**
 * GENERATED from App\Schemas\Platform\Addresses\AddressesSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 11/10/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Platform\Addresses\Models;

use App\Shared\Schema\BaseModel;

final class Addresses extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $owner_type_id;
    public int $owner_id;
    public string $purpose;
    public string $address;
    public ?string $zip_code;
    public ?int $city_id;
    public ?int $state_id;
    public ?int $country_id;
    public ?string $neighborhood;
    public ?string $complement;
    public ?string $reference;
    public ?string $extra_info;
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
        $newObject->purpose = isset($data['purpose']) ? $data['purpose'] : null;
        $newObject->address = isset($data['address']) ? $data['address'] : null;
        $newObject->zip_code = isset($data['zip_code']) ? $data['zip_code'] : null;
        $newObject->city_id = isset($data['city_id']) ? (int) $data['city_id'] : null;
        $newObject->state_id = isset($data['state_id']) ? (int) $data['state_id'] : null;
        $newObject->country_id = isset($data['country_id']) ? (int) $data['country_id'] : null;
        $newObject->neighborhood = isset($data['neighborhood']) ? $data['neighborhood'] : null;
        $newObject->complement = isset($data['complement']) ? $data['complement'] : null;
        $newObject->reference = isset($data['reference']) ? $data['reference'] : null;
        $newObject->extra_info = isset($data['extra_info']) ? $data['extra_info'] : null;
        return $newObject;
    }
}