<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Schemas\Platform\Ownership;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, Comment, Nullable};

final class OwnableTypesSchema extends AbstractSchema
{
    public string $table = 'ownable_types';
    
    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: false)]
    #[Comment(comment: 'Table name of the ownable object, eg: users, tenants, projects, etc.')]
    public ?string $name;
}