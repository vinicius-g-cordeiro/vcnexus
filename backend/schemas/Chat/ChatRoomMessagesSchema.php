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
use App\Shared\Schema\Attributes\{Column, ForeignKey, Nullable};


#[ForeignKey(name: 'fk_chat_messages_room', foreignKeys: ['room_id'], references: 'chat_rooms', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE', deferred: true)]
#[ForeignKey(name: 'fk_chat_messages_user', foreignKeys: ['user_id'], references: 'user_credentials', columns: ['id'], actionOnUpdate: false, deferred: true)]
final class ChatRoomMessagesSchema extends AbstractSchema
{
    public string $table = 'chat_messages';

    #[Column(type: 'bigint')]
    #[Nullable(nullable: false)]
    public ?int $room_id;


    #[Column(type: 'bigint')]
    #[Nullable(nullable: false)]
    public ?int $user_id;


    #[Column(type: 'text')]
    #[Nullable(nullable: false)]
    public ?string $content;


    #[Column(type: 'timestamp')]
    #[Nullable(nullable: true)]
    public ?string $created_at;


}
