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
use App\Shared\Schema\Attributes\{Column, Nullable, TenantScoped};


#[TenantScoped(tenant_id: 'tenant_id', nullable: true)]
final class ChatRoomSchema extends AbstractSchema
{
    public string $table = 'chat_rooms';

    #[Column(type: 'timestamp')]
    #[Nullable(nullable: false)]
    public ?string $created_at;

}
