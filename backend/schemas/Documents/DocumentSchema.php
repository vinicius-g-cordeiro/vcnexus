<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Schemas\Documents;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\Column;
use App\Shared\Schema\Attributes\Nullable;
use App\Shared\Schema\Attributes\Auditable;
use App\Shared\Schema\Attributes\ForeignKey;
use App\Shared\Schema\Attributes\Timestamps;

#[Timestamps(created_at: 'created_at', updated_at: 'updated_at', deleted_at: 'deleted_at')]
#[Auditable(created_by: 'created_by', updated_by: 'updated_by', deleted_by: 'deleted_by', deleted_reason: 'deleted_reason')]
#[ForeignKey(name: 'fk_documents_document_types', foreignKeys: ['type_id'], references: 'document_types', columns: ['id'], actionOnUpdate: false, deleteAction: 'SET NULL')]
final class DocumentSchema extends AbstractSchema
{
    public string $table = 'documents';

    #[Column(type: 'smallint', length: 1, default: 1)]
    #[Nullable(nullable: false)]
    public ?int $name;

    #[Column(type: 'smallint', length: 1, default: 1)]
    #[Nullable(nullable: true)]
    public ?int $label;

    #[Column(type: 'varchar', length: 255, default: null)]
    #[Nullable(nullable: true)]
    public string $tags;

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: true)]
    public ?string $slug;

    #[Column(type: 'varchar', length: 500, default: null)]
    #[Nullable(nullable: false)]
    public ?string $path;

    #[Column(type: 'bigint', default: null)]
    #[Nullable(nullable: false)]
    public ?string $type_id;

    #[Column(type: 'bigint', default: null)]
    #[Nullable(nullable: false)]
    public ?string $owner_type_id;

    #[Column(type: 'bigint', default: null)]
    #[Nullable(nullable: false)]
    public ?string $owner_id;
}
