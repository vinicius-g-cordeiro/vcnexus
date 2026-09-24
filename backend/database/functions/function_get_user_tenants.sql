drop function if exists public.get_user_tenants;

CREATE OR REPLACE FUNCTION public.get_user_tenants(p_user_id BIGINT)
RETURNS TABLE (
	uuid UUID,
	id BIGINT,
    name VARCHAR,
    email VARCHAR,
    avatar VARCHAR,
    tenants BIGINT[],
    roles BIGINT[],
    permissions VARCHAR[]
)
LANGUAGE sql
SECURITY DEFINER
SET search_path = public
AS $$
    SELECT
		uc.uuid,
		uc.id,
        (SELECT firstname FROM public.user_profile WHERE user_id = uc.id LIMIT 1) AS name,
        uc.email,
        '' as avatar,
        (SELECT array_agg(t.id) FROM public.tenant_memberships AS t WHERE t.user_id = uc.id AND t.active = 1) AS tenants,
        (SELECT array_agg(r.id) FROM public.user_roles AS r WHERE r.user_id = uc.id   AND r.active = 1) AS roles,
        (SELECT array_agg(p.slug) FROM public.permissions AS p INNER JOIN public.user_permissions AS up on p.id = up.permission_id WHERE up.user_id = uc.id  AND up.active = 1) AS permissions
    FROM public.user_credentials AS uc
    WHERE uc.id = p_user_id 
    LIMIT 1;
$$;

REVOKE ALL
ON FUNCTION public.get_user_tenants(BIGINT)
FROM PUBLIC;

GRANT EXECUTE
ON FUNCTION public.get_user_tenants(BIGINT)
TO app_user;