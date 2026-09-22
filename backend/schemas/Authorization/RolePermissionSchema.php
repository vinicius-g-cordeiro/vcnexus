<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Schemas\Authorization;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Column, Nullable};


final class RolePermissionSchema extends AbstractSchema
{
    public string $table = 'role_permissions';

    #[Column(type: 'bigint')]
    #[Nullable(nullable: false)]
    public ?int $role_id = 1;


    #[Column(type: 'bigint')]
    #[Nullable(nullable: false)]
    public ?int $permission_id = 1;

}
