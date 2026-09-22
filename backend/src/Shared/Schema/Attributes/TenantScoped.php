<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Schema\Attributes;

use Attribute;

/**
 * #[TenantScoped]
 * final class UserSchema {
 * }
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class TenantScoped {

    #[Column(type: 'bigint', default: null)]
    public ?int $tenant_id;

    public bool $nullable = false;

    public function __construct(string $tenant_id = 'tenant_id', bool $nullable = false) {
        if(isset($tenant_id) === false){
            unset($this->tenant_id);
        }else{
            $this->tenant_id = 1;
        }

        $this->nullable = $nullable;
    }
}