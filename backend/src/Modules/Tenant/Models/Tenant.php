<?php
/**
 * GENERATED from App\Schemas\Tenant\TenantSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 24/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Tenant\Models;

use App\Shared\Schema\BaseModel;

final class Tenant extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $subscription_type;
    public int $subscription_status;
    public ?string $domain;
    public string $slug;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $deleted_at;
    public ?int $created_by;
    public ?int $updated_by;
    public ?int $deleted_by;
    public ?string $deleted_reason;
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
        $newObject->subscription_type = isset($data['subscription_type']) ? (int) $data['subscription_type'] : null;
        $newObject->subscription_status = isset($data['subscription_status']) ? (int) $data['subscription_status'] : null;
        $newObject->domain = isset($data['domain']) ? $data['domain'] : null;
        $newObject->slug = isset($data['slug']) ? $data['slug'] : null;
        $newObject->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $newObject->updated_at = isset($data['updated_at']) ? $data['updated_at'] : null;
        $newObject->deleted_at = isset($data['deleted_at']) ? $data['deleted_at'] : null;
        $newObject->created_by = isset($data['created_by']) ? (int) $data['created_by'] : null;
        $newObject->updated_by = isset($data['updated_by']) ? (int) $data['updated_by'] : null;
        $newObject->deleted_by = isset($data['deleted_by']) ? (int) $data['deleted_by'] : null;
        $newObject->deleted_reason = isset($data['deleted_reason']) ? $data['deleted_reason'] : null;
        return $newObject;
    }
}