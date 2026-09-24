<?php
/**
 * GENERATED from App\Schemas\Chat\ChatRoomMessagesSchema — do not edit directly. Edit the schema and recompile.
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0.0
 * @date 24/09/2026
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Chat\Models;

use App\Shared\Schema\BaseModel;

final class ChatRoomMessages extends BaseModel
{
    public ?int $id;
    public ?string $uuid;
    public ?int $active;
    public int $room_id;
    public int $user_id;
    public string $content;
    public ?string $created_at;
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
        $newObject->room_id = isset($data['room_id']) ? (int) $data['room_id'] : null;
        $newObject->user_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $newObject->content = isset($data['content']) ? $data['content'] : null;
        $newObject->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        return $newObject;
    }
}