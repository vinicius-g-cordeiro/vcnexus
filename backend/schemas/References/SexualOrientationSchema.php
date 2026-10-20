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


final class SexualOrientationSchema extends AbstractSchema
{
    public string $table = 'sexual_orientations';
    
    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: false)]
    public ?string $name;
    
    
    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable]
    public ?string $description;

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: false)]
    public ?string $label;

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: true)]
    #[Comment('eg: icon from fontawesome or bootstrap icons... ')]
    public ?string $icon;
}