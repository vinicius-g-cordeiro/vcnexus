drop function if exists public.get_user_permissions;
CREATE OR REPLACE FUNCTION public.get_user_permissions(p_user_uuid UUID) RETURNS TABLE (
        permission_id BIGINT,
        user_id BIGINT,
        id BIGINT,
        uuid UUID,
        name VARCHAR,
        slug VARCHAR,
        description VARCHAR,
        tenant_id BIGINT,
        active SMALLINT,
        created_at TIMESTAMP
    ) LANGUAGE sql SECURITY DEFINER
SET search_path = public AS $$
SELECT up.permission_id, up.user_id, up.id, p.uuid, p.name, p.slug, p.description, p.tenant_id, p.active, p.created_at
            FROM user_permissions up
            INNER JOIN user_credentials uc ON uc.id = up.user_id
            INNER JOIN permissions p ON p.id = up.permission_id
WHERE uc.uuid = p_user_uuid
$$;
REVOKE ALL ON FUNCTION public.get_user_permissions(UUID)
FROM PUBLIC;
GRANT EXECUTE ON FUNCTION public.get_user_permissions(UUID) TO app_user;
