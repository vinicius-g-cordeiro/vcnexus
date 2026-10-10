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
use App\Shared\Schema\Attributes\{Column, Comment, Nullable};


#[Comment(comment: 'Level of completion of something, eg: Completed, In progress, Not started, On Hold, etc..')]
final class CompletionStatusSchema extends AbstractSchema
{
    public string $table = 'completion_statuses';
    
    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: false)]
    public ?string $name;

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: false)]
    public ?string $label;
    
    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable]
    public ?string $description;

    #[Column(type: 'smallint', default: null)]
    #[Nullable]
    #[Comment(comment: 'Type of completion status, to be used for filtering, if null, it will be used for anything thats needs a completion status, eg: 1: Educational Levels, 2: Tasks, 3: Deliveries, etc...')]
    public ?int $type;
}