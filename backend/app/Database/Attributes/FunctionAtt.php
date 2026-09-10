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
class FunctionAtt
{
    /**
     * @param string $name - 'fn_get_tenants
     * @param string $tsql =  
     * @example FKConstraint:         
        CREATE OR REPLACE FUNCTION public.get_user_tenants(p_user_id BIGINT)
            RETURNS TABLE (
                tenant_id BIGINT,
                roles VARCHAR[]
            )
            LANGUAGE sql
            SECURITY DEFINER
            SET search_path = public
            AS $$
                SELECT
                    tu.tenant_id,
                    tu.roles
                FROM public.tenant_users AS tu
                WHERE tu.user_id = p_user_id;
            $$;

            REVOKE ALL
            ON FUNCTION public.get_user_tenants(BIGINT)
            FROM PUBLIC;

            GRANT EXECUTE
            ON FUNCTION public.get_user_tenants(BIGINT)
        TO app_user;
     */
    function __construct(public readonly string $name = 'fn', public readonly ?string $tsql = '' ) {
    }


}