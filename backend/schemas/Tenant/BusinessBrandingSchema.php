<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Schemas\Tenant;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, ForeignKey, Nullable};

#[ForeignKey(name: 'fk_business_branding', foreignKeys: ['business_id'], references: 'business', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE')]
class BusinessBrandingSchema extends AbstractSchema
{
    public string $table = 'business_brandings';

    #[Column(type: 'bigint', default: 1)]
    public ?int $business_id = 1;

    #[Column(type: 'varchar', length: 255, default: '')]
    #[Nullable(nullable: true)]
    public ?string $logo = '';

    #[Column(type: 'varchar', length: 255, default: null)]
    #[Nullable(nullable: true)]
    public ?string $app_name = '';

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable(nullable: true)]
    public ?string $primary_color = '';
    
    #[Column(type: 'varchar', length: 100, default: null)]
    public ?string $accent_color = '';

    #[Column(type: 'varchar', length: 100, default: null)]
    public ?string $text_color = '';

    #[Column(type: 'varchar', length: 100, default: null)]
    public ?string $background_color = '';

    #[Column(type: 'varchar', length: 100, default: null)]
    public ?string $font_style = '';

    #[Column(type: 'varchar', length: 100, default: null)]
    public ?string $button_style = '';
}
