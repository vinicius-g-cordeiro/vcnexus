<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Schemas\References;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, ForeignKey, Nullable};


#[ForeignKey(name: 'fk_educational_level_educational_type', foreignKeys: ['educational_type_id'], columns: ['id'], references: 'educational_types', deleteAction: 'SET NULL', actionOnUpdate: false, deferred: true)]
final class EducationalLevelSchema extends AbstractSchema
{
    public string $table = 'educational_levels';
    
    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: false)]
    public ?string $name;

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: false)]
    public ?string $label;
    
    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable]
    public ?string $description;

    #[Column(type: 'smallint')]
    #[Nullable]
    public ?string $priority;

    #[Column(type: 'bigint', default: null)]
    #[Nullable(nullable: true)]
    public ?int $educational_type_id;
}