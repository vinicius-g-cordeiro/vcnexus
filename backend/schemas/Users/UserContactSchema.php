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

#[ForeignKey(name: 'fk_user_contact_user_credentials', foreignKeys: ['user_id'], references: 'user_credentials', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE', deferred: true)]
final class UserContactSchema extends AbstractSchema
{
    public string $table = 'user_contact';

    #[Column(type: 'bigint')]
    public readonly int $user_id;

    #[Column(type: 'smallint')]
    #[Comment(comment: 'eg: 1: Email, 2: Phone, 3: Website, ...')]
    public readonly ?int $type;

    #[Column(type: 'varchar', length: 100)]
    public readonly string $value;

    #[Column(type: 'varchar', length: 100)]
    public readonly string $label;

    #[Column(type: 'smallint')]
    public readonly int $primary_contact;

    // If it's home, work, references, etc..
    #[Column(type: 'varchar', length: 100)]
    #[Nullable]
    #[Comment(comment: 'eg: Home, Work, Reference, etc..')]
    public readonly ?string $category;

    #[Column(type: 'varchar', length: 100)]
    #[Nullable]
    #[Comment(comment: 'eg: Jociely(Wife)')]
    public readonly ?string $person;
}

