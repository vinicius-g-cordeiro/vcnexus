<?php
/**
 * GENERATED from App\Schemas\References\StateSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 10/10/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\References\Models;

use App\Shared\Schema\BaseModel;

final class State extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public string $name;
    public ?string $description;
    public string $code;
    public int $country_id;
    public ?int $iso_alpha2;
    public ?int $iso_alpha3;
    public int $geonames_id;
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
        $newObject->code = isset($data['code']) ? $data['code'] : null;
        $newObject->country_id = isset($data['country_id']) ? (int) $data['country_id'] : null;
        $newObject->iso_alpha2 = isset($data['iso_alpha2']) ? (int) $data['iso_alpha2'] : null;
        $newObject->iso_alpha3 = isset($data['iso_alpha3']) ? (int) $data['iso_alpha3'] : null;
        $newObject->geonames_id = isset($data['geonames_id']) ? (int) $data['geonames_id'] : null;
        return $newObject;
    }
}