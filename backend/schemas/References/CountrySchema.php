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
use App\Shared\Schema\Attributes\{Column, Nullable, Unique};


#[Unique(name: 'uq_countries_geonames_id', columns: ['geonames_id'])]
final class CountrySchema extends AbstractSchema
{
    public string $table = 'countries';
    
    #[Column(type: 'varchar', length: 100)]    
    public ?string $name;
    
    #[Column(type: 'varchar', length: 200)]
    #[Nullable]
    public ?string $description;

    #[Column(type: 'bigint')]
    #[Nullable]
    public ?string $geonames_id;

        #[Column(type: 'varchar')]
    #[Nullable(nullable: true)]
    public ?string $iso_alpha2;

    #[Column(type: 'varchar')]
    #[Nullable(nullable: true)]
    public ?string $iso_alpha3;
}