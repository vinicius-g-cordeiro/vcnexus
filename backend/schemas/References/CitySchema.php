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


#[Unique(name: 'uq_cities_geonames_id', columns: ['geonames_id'])]
final class CitySchema extends AbstractSchema
{
    public string $table = 'cities';
    
    #[Column(type: 'varchar', length: 100)]    
    public ?string $name;
    
    #[Column(type: 'varchar', length: 200)]
    #[Nullable(nullable: true)]
    public ?string $description;

    #[Column(type: 'bigint')]
    #[Nullable(nullable: false)]
    public ?int $geonames_id;

    #[Column(type: 'bigint')]
    #[Nullable(nullable: true)]
    public ?int $iso_alpha2;

    #[Column(type: 'bigint')]
    #[Nullable(nullable: true)]
    public ?int $iso_alpha3;

    #[Column(type: 'bigint')]
    #[Nullable(nullable: true)]
    public ?int $state_id;

    #[Column(type: 'bigint')]
    #[Nullable(nullable: true)]
    public ?int $country_id;
}