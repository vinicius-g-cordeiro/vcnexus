<?php
/**
 * GENERATED from App\Schemas\Users\UserConsentsSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 22/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Users\Models;

use App\Shared\Schema\BaseModel;

final class UserConsents extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $user_id;
    public string $purpose;
    public string $legal_basis;
    public string $granted_at;
    public ?string $revoked_at;
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
        $newObject->purpose = isset($data['purpose']) ? $data['purpose'] : null;
        $newObject->legal_basis = isset($data['legal_basis']) ? $data['legal_basis'] : null;
        $newObject->granted_at = isset($data['granted_at']) ? $data['granted_at'] : null;
        $newObject->revoked_at = isset($data['revoked_at']) ? $data['revoked_at'] : null;
        return $newObject;
    }
}