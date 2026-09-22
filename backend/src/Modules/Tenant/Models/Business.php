<?php
/**
 * GENERATED from App\Schemas\Tenant\BusinessSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 22/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Tenant\Models;

use App\Shared\Schema\BaseModel;

final class Business extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public string $trade_name;
    public string $fantasy_name;
    public string $tax_id;
    public ?int $type;
    public ?string $state_registration;
    public ?string $municipal_registration;
    public ?string $description;
    public ?string $website;
    public int $tenant_id;
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
        $newObject->trade_name = isset($data['trade_name']) ? $data['trade_name'] : null;
        $newObject->fantasy_name = isset($data['fantasy_name']) ? $data['fantasy_name'] : null;
        $newObject->tax_id = isset($data['tax_id']) ? $data['tax_id'] : null;
        $newObject->type = isset($data['type']) ? (int) $data['type'] : null;
        $newObject->state_registration = isset($data['state_registration']) ? $data['state_registration'] : null;
        $newObject->municipal_registration = isset($data['municipal_registration']) ? $data['municipal_registration'] : null;
        $newObject->description = isset($data['description']) ? $data['description'] : null;
        $newObject->website = isset($data['website']) ? $data['website'] : null;
        $newObject->tenant_id = isset($data['tenant_id']) ? (int) $data['tenant_id'] : null;
        return $newObject;
    }
}