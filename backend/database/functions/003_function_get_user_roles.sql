drop function if exists public.get_user_roles;
CREATE OR REPLACE FUNCTION public.get_user_roles(p_user_uuid UUID) RETURNS TABLE (
        role_id BIGINT,
        user_id BIGINT,
        id BIGINT,
        uuid UUID,
        name VARCHAR,
        description VARCHAR,
        tenant_id BIGINT,
        active SMALLINT,
        created_at TIMESTAMP
    ) LANGUAGE sql SECURITY DEFINER
SET search_path = public AS $$
SELECT ur.role_id,
    ur.user_id,
    ur.id,
    r.uuid,
    r.name,
    r.description,
    r.tenant_id,
    r.active,
    r.created_at
FROM user_roles ur
    INNER JOIN user_credentials uc ON uc.id = ur.user_id
    INNER JOIN roles r ON r.id = ur.role_id
WHERE uc.uuid = p_user_uuid
LIMIT 1;
$$;
REVOKE ALL ON FUNCTION public.get_user_roles(UUID)
FROM PUBLIC;
GRANT EXECUTE ON FUNCTION public.get_user_roles(UUID) TO app_user;
