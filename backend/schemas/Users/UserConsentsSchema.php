<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Schemas\Users;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, Comment, Nullable, ForeignKey};

#[ForeignKey(name: 'fk_user_consents_user_credentials', foreignKeys: ['user_id'], references: 'user_credentials', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE', deferred: true)]
final class UserConsentsSchema extends AbstractSchema
{
    public string $table = 'user_consents';

    #[Column(type: 'bigint')]
    public readonly int $user_id;

    #[Column(type: 'varchar', length: 64)]
    #[Comment(comment: 'eg: \'diversity reporting\', \'legal compliance\'')]
    public readonly string $purpose;

    #[Column(type: 'varchar', length: 32)]
    #[Comment(comment: 'eg: \'legal obligation\' , \'consent\'')]
    public readonly string $legal_basis;

    #[Column(type: 'timestamp', default: 'current_timestamp')]
    public readonly ?string $granted_at;

    #[Column(type: 'timestamp', default: null)]
    #[Nullable(nullable: true)]
    public readonly ?string $revoked_at;
}

