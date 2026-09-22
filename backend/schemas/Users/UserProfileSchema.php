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

#[ForeignKey(name: 'fk_user_profile_user_credentials', foreignKeys: ['user_id'], references: 'user_credentials', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE', deferred: true)]
final class UserProfileSchema extends AbstractSchema
{
    public string $table = 'user_profile';

    #[Column(type: 'bigint')]
    public readonly int $user_id;

    #[Column(type: 'varchar', length: 100)]
    public readonly string $firstname;

    #[Column(type: 'varchar', length: 100)]
    #[Nullable(nullable: true)]
    public readonly ?string $surname;

    #[Column(type: 'varchar', length: 100)]
    public readonly string $lastname;

    #[Column(type: 'smallint', length: 1, default: null)]
    #[Nullable(nullable: true)]
    #[Comment(comment: '1 = Single, 2 = Married, 3 = Divorced, 4 = Widowed, 5 = Stable Union, 6 = Others, null = Unknown, see: marital_status table')]
    public readonly ?int $marital_status_id;

    #[Column(type: 'timestamp', default: null)]
    #[Nullable(nullable: true)]
    public readonly ?string $birthdate;

    #[Column(type: 'smallint', length: 1)]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'see: nationality table')]
    public readonly ?int $nationality_id; 

    #[Column(type: 'varchar', length: 5, default: 'en-US')]
    #[Nullable(nullable: true)]
    public readonly ?string $locale;
}