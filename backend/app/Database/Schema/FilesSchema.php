<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Database\Schema;

use App\Database\Attributes\Column;
use App\Database\Attributes\ForeignKeyConstraint;


#[ForeignKeyConstraint(name: 'fk_files_tenant_users_created_by', foreignKeys: ['created_by'], references: 'tenant_users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_files_tenant_users_updated_by', foreignKeys: ['updated_by'], references: 'tenant_users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
#[ForeignKeyConstraint(name: 'fk_files_tenant_users_deleted_by', foreignKeys: ['deleted_by'], references: 'tenant_users', columns: ['id'], actionOnDelete: true, deleteAction: 'SET NULL', deferred:true)]
final class FilesSchema extends Schema
{
    public string $table = 'files';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: false, comment: '')]
    public ?string $name = '';

    #[Column(type: 'VARCHAR(500)', default: 1, nullable: true)]
    public ?string $description = '';

    #[Column(type: 'SMALLINT', default: '1', nullable: false, comment: 'Type of document. ex: 1: Document, 2: Photo/Avatar, 3: Contract, 4: Video, 5: Audio, etc..')]
    public ?int $type = 1;

    #[Column(type: 'BIGINT', default: null, nullable: true, comment: 'A user id if the file has a relation to the tenant_user table, such as documents, avatars, etc.. ')]
    public ?int $user_id = null;

    #[Column(type: 'BIGINT', default: null, nullable: true, inherit:true, comment: 'Tenant id if the file has relation to the tenant table, for example the subscription contract for the tenant')]
    public ?int $tenant_id = null;

    #[Column(type: 'VARCHAR(255)', default: 1, nullable: false)]
    public ?string $path = '';

    #[Column(type: 'VARCHAR(500)', default: 1, nullable: false)]
    public ?string $full_path = '';

    #[Column(type: 'VARCHAR(255)', default: 1, nullable: false)]
    public ?string $extension = '';

    #[Column(type: 'VARCHAR(255)', default: 1, nullable: false)]
    public ?string $mime_type = '';

    public function __construct()
    {
        parent::__construct();
    }
}