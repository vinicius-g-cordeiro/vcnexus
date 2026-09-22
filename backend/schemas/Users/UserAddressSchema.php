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
use App\Shared\Schema\Attributes\{Column, Comment, Nullable, ForeignKey};

#[ForeignKey(name: 'fk_user_address_user_credentials', foreignKeys: ['user_id'], references: 'user_credentials', columns: ['id'], actionOnUpdate: true, deleteAction: 'CASCADE', deferred: true)]
final class UserAddressSchema extends AbstractSchema
{
    public string $table = 'user_address';

    #[Column(type: 'bigint')]
    public readonly int $user_id;

    #[Column(type: 'varchar')]
    #[Comment(comment: 'eg: Home, Work, etc..')]
    public readonly string $purpose;

    #[Column(type: 'varchar', length: 255)]
    public readonly string $address;

    #[Column(type: 'varchar', length: 15)]
    #[Nullable]
    public readonly ?string $zip_code;

    #[Column(type: 'bigint')]
    #[Nullable]
    public readonly ?int $city_id;

    #[Column(type: 'bigint')]
    #[Nullable]
    public readonly ?int $state_id;

    #[Column(type: 'bigint')]
    #[Nullable]
    public readonly ?int $country_id;

    #[Column(type: 'varchar', length: 255)]
    #[Nullable]
    public readonly ?string $neighborhood;

    #[Column(type: 'varchar', length: 255)]
    #[Nullable]
    public readonly ?string $complement;

    #[Column(type: 'varchar', length: 255)]
    #[Nullable]
    public readonly ?string $reference;

    #[Column(type: 'varchar', length: 500)]
    #[Nullable]
    public readonly ?string $extra_info;
}

