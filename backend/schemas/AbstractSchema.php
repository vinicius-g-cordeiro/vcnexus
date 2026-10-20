<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Schemas;

use App\Shared\Schema\Attributes\{Column, Comment, Identity, Nullable, PrimaryKey};

abstract class AbstractSchema {

    public string $table = 'example';

    #[Column(type: 'bigint', default: 1)]
    #[Identity(generated: 'ALWAYS')]
    #[PrimaryKey(primaryKey: true, key: 'id')]
    #[Nullable(nullable: true)]
    public ?int $id;

    #[Column(type: 'UUID', default: 'uuidv7()')]
    #[Comment('V7 UUID')]
    public readonly ?string $uuid;
    
    #[Column(type: 'smallint', length: 1, default: 1)]
    public readonly ?int $active;

}

