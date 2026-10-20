<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Schemas\Chat;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, Identity, Index, Inherited, References, Nullable, PrimaryKey};

#[Index(name: 'idx_chat_room_users_room_id', columns: ['room_id'], references: 'chat_room_users')]
#[PrimaryKey(primaryKey: true, key: ['user_id', 'room_id'])]
final class ChatRoomUsersSchema extends AbstractSchema
{
    public string $table = 'chat_room_users';

    #[Inherited(inherited: true)]
    #[Column(type: 'bigint')]
    #[Nullable(nullable: false)]
    #[PrimaryKey(primaryKey: true, key: null)]
    #[Identity(identity: true)]
    public ?int $id;
    
    #[Column(type: 'bigint')]
    #[Nullable(nullable: true)]
    #[References(references: 'user_credentials', columns: ['id'], deleteAction: 'CASCADE')]
    public ?int $user_id;


    #[Column(type: 'bigint')]
    #[Nullable(nullable: true)]
    #[References(references: 'chat_rooms', columns: ['id'], deleteAction: 'CASCADE')]
    public ?int $room_id;

}
