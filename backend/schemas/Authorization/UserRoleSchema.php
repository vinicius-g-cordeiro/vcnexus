<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Schemas\Authorization;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, Nullable, Auditable, Timestamps, ForeignKey};


#[Auditable(created_by: 'created_by', updated_by: 'updated_by')]
#[Timestamps(created_at: 'created_at', updated_at: 'updated_at', deleted_at: null)]
#[ForeignKey(name: 'fk_user_address_user_credentials', foreignKeys: ['user_id'], references: 'user_credentials', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE', deferred: true)]
final class UserRoleSchema extends AbstractSchema
{
    public string $table = 'user_roles';

    #[Column(type: 'bigint')]
    #[Nullable(nullable: false)]
    public ?int $user_id = 1;

    #[Column(type: 'bigint')]
    #[Nullable(nullable: false)]
    public ?int $role_id = 1;
}
