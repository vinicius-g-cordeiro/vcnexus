<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Database\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Policy
{
    /**
     * @param string $name - 'fk_users_tenant
     * @param bool $restrictive - if should be restrictive
     * @param string $table = 'table_name'
     * @param string $for for select
     * @param string $toUser - app_user
     * @param array<string>[string:string[]] $using = [<string>user_id => [
     *  key => 'app.user_id'
     *  type => bigint
     * ]]
     * @param array<string>[string:string[]] $withCheck = [<string>user_id => [
     *  key => 'app.user_id'
     *  type => bigint
     * ]]
     * @example FKConstraint:         
        CREATE POLICY tenant_membership
        ON public.tenant_users
        AS RESTRICTIVE
        FOR SELECT
        TO app_user
        USING (
            user_id = current_setting('app.user_id')::bigint
        );
        WITH CHECK (
            user_id = current_setting('app.user_id')::bigint
        );
     */
    function __construct(public readonly string $name = 'tenant_membership', public readonly ?string $table = 'tenant_users', public readonly ?bool $restrictive = false, public readonly ?string $for = 'SELECT', public readonly ?string $toUser = 'app_user', public readonly ?array $using = null, public readonly ?array $withCheck = null) {
    }


}