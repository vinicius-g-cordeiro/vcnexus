drop function if exists public.get_tenant_memberships_access;

CREATE OR REPLACE FUNCTION public.get_tenant_memberships_access(p_user_id BIGINT)
RETURNS TABLE (
	tenant_id BIGINT,
    roles BIGINT[],
    permissions BIGINT[]
)
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
AS $$
    SELECT
        tm.tenant_id,
        (SELECT array_agg(r.id) FROM public.user_roles AS r WHERE r.user_id = uc.id AND r.active = 1) AS roles,
        (SELECT array_agg(up.id) FROM public.user_permissions AS up WHERE up.user_id = uc.id AND up.active = 1) AS permissions
    FROM public.tenant_memberships AS tm
    INNER JOIN public.user_credentials AS uc ON uc.id = tm.user_id
    WHERE uc.id = p_user_id 
    LIMIT 1;
$$;

REVOKE ALL
ON FUNCTION public.get_tenant_memberships_access(BIGINT)
FROM PUBLIC;

GRANT EXECUTE
ON FUNCTION public.get_tenant_memberships_access(BIGINT)
TO app_user;