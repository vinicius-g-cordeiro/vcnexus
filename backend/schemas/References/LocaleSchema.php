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


final class LocaleSchema extends AbstractSchema
{
    public string $table = 'locales';
    
    #[Column(type: 'varchar', length: 100)]    
    public ?string $name;

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: false)]
    public ?string $label;
    
    #[Column(type: 'varchar', length: 200)]
    #[Nullable(nullable: true)]
    public ?string $description;
 
    #[Column(type: 'varchar', length: 255)]
    public ?string $i18n_path;

    #[Column(type: 'varchar', length: 255)]
    #[Comment(comment: 'locale flag image path url')]
    public ?string $flag_path;
}