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
use App\Shared\Schema\Attributes\{Column, Comment, Nullable, Timestamps, ForeignKey};

#[ForeignKey(name: 'fk_user_sensitive_user_credentials', foreignKeys: ['user_id'], references: 'user_credentials', columns: ['id'], actionOnUpdate: false, deleteAction: 'CASCADE', deferred: true)]
#[Timestamps(created_at: null, updated_at: 'updated_at', deleted_at: null)]
final class UserSensitiveSchema extends AbstractSchema
{
    public string $table = 'user_sensitive';

    #[Column(type: 'bigint')]
    public readonly int $user_id;

    #[Column(type: 'varchar', length: 100)]
    #[Nullable(nullable: true)]
    public readonly ?string $socialname;

    #[Column(type: 'smallint')]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'see: gender table')]
    public readonly ?int $gender_id;

    #[Column(type: 'smallint', length: 1)]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'see: religions table')]
    public readonly ?int $religion_id;
    
    #[Column(type: 'smallint', length: 1)]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'see: ethnicity table')]
    public readonly ?int $ethnicity_id; 

    #[Column(type: 'smallint', length: 1)]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'see: sexual_orientation table')]
    public readonly ?int $sexual_orientation;

    #[Column(type: 'smallint', length: 1)]
    #[Nullable(nullable: true)]
    #[Comment(comment: 'see: disabilities table')]
    public readonly ?int $disability_id;

}