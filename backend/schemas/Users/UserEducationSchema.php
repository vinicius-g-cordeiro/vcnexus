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
use App\Shared\Schema\Attributes\{Column, Nullable, ForeignKey};

#[ForeignKey(name: 'fk_user_education_user_credentials', foreignKeys: ['user_id'], references: 'user_credentials', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE', deferred: true)]
#[ForeignKey(name: 'fk_user_education_educational_level', foreignKeys: ['educational_level_id'], references: 'educational_levels', columns: ['id'], actionOnUpdate: false, deleteAction: 'SET NULL', deferred: true)]
#[ForeignKey(name: 'fk_user_education_educational_type', foreignKeys: ['educational_type_id'], references: 'educational_types', columns: ['id'], actionOnUpdate: false, deleteAction: 'SET NULL', deferred: true)]
#[ForeignKey(name: 'fk_user_education_completion_status', foreignKeys: ['completion_status_id'], references: 'completion_statuses', columns: ['id'], actionOnUpdate: false, deleteAction: 'SET NULL', deferred: true)]
final class UserEducationSchema extends AbstractSchema
{
    public string $table = 'user_education';

    #[Column(type: 'bigint')]
    public readonly int $user_id;

    #[Column(type: 'bigint')]
    #[Nullable]
    public readonly ?int $educational_level_id;

    #[Column(type: 'bigint')]
    #[Nullable]
    public readonly ?int $educational_type_id;
    
    #[Column(type: 'bigint')]
    public readonly ?int $completion_status_id;

    #[Column(type: 'varchar', length: 255)]
    public readonly ?string $institution;

    #[Column(type: 'varchar', length: 255)]
    public readonly ?string $name;

    #[Column(type: 'timestamp')]
    public readonly ?string $start_date;

    #[Column(type: 'timestamp')]
    #[Nullable]
    public readonly ?string $end_date;

    #[Column(type: 'timestamp')]
    #[Nullable]
    public readonly ?string $expiration_date;

    #[Column(type: 'varchar')]
    #[Nullable]
    public readonly ?string $certification_number;

    #[Column(type: 'varchar', length: 255)]
    #[Nullable]
    public readonly ?string $certification_url;

    #[Column(type: 'text')]
    #[Nullable]
    public readonly ?string $description;
}