<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Schemas\Platform\Contacts;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, Comment, Nullable, ForeignKey};

#[ForeignKey(name: 'fk_contacts_ownable_types', foreignKeys: ['owner_type_id'], references: 'ownable_types', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE', deferred: true)]
#[ForeignKey(name: 'fk_contacts_contact_category', foreignKeys: ['category_id'], references: 'contact_categories', columns: ['id'], actionOnUpdate: true, deleteAction: 'SET NULL', deferred: true)]
#[ForeignKey(name: 'fk_contacts_contact_type', foreignKeys: ['type_id'], references: 'contact_types', columns: ['id'], actionOnUpdate: true, deleteAction: 'SET NULL', deferred: true)]
final class ContactsSchema extends AbstractSchema
{
    public string $table = 'contacts';

    #[Column(type: 'bigint')]
    public readonly int $owner_type_id;

    #[Column(type: 'bigint')]
    public readonly int $owner_id;

    #[Column(type: 'smallint')]
    #[Comment(comment: 'eg: 1: Email, 2: Phone, 3: Website, ... see contact_types table')]
    public readonly ?int $type_id;

    #[Column(type: 'varchar', length: 100)]
    public readonly string $value;

    #[Column(type: 'varchar', length: 100)]
    public readonly string $label;

    #[Column(type: 'smallint')]
    public readonly int $primary_contact;

    // If it's home, work, references, etc..
    #[Column(type: 'bigint')]
    #[Nullable]
    #[Comment(comment: 'eg: Home, Work, Reference, etc.., see contact_categories table')]
    public readonly ?int $category_id;

    #[Column(type: 'varchar', length: 100)]
    #[Nullable]
    #[Comment(comment: 'eg: person name: Jhon doe (brother), jane doe (sister), etc...')]
    public readonly ?string $person;
}

