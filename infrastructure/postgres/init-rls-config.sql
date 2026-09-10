BEGIN;

-- ============================================================
-- APPLICATION ROLE
-- ============================================================

ALTER ROLE app_user
    NOSUPERUSER
    NOCREATEDB
    NOCREATEROLE
    NOBYPASSRLS;

-- ============================================================
-- SCHEMA ACCESS
-- ============================================================

GRANT USAGE
ON SCHEMA public
TO app_user;

-- ============================================================
-- EXISTING TABLE ACCESS
-- ============================================================

GRANT SELECT, INSERT, UPDATE, DELETE
ON ALL TABLES IN SCHEMA public
TO app_user;

-- ============================================================
-- EXISTING SEQUENCE ACCESS
-- Required for SERIAL / BIGSERIAL / identity-based inserts
-- ============================================================

GRANT USAGE, SELECT
ON ALL SEQUENCES IN SCHEMA public
TO app_user;

-- ============================================================
-- DEFAULT PRIVILEGES
--
-- IMPORTANT:
-- These apply to objects CREATED BY app_root.
-- If another owner/migration role creates tables, run the
-- corresponding ALTER DEFAULT PRIVILEGES for that role too.
-- ============================================================

ALTER DEFAULT PRIVILEGES IN SCHEMA public
GRANT SELECT, INSERT, UPDATE, DELETE
ON TABLES
TO app_user;

ALTER DEFAULT PRIVILEGES IN SCHEMA public
GRANT USAGE, SELECT
ON SEQUENCES
TO app_user;

-- ============================================================
-- USERS TABLE
-- ============================================================

ALTER TABLE public.tenant_users
    ENABLE ROW LEVEL SECURITY;

ALTER TABLE public.tenant_users
    FORCE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS tenant_isolation
ON public.tenant_users;

CREATE POLICY tenant_isolation
ON public.tenant_users
AS RESTRICTIVE
FOR ALL
TO app_user
USING (
    tenant_id = current_setting('app.tenant_id')::bigint
)
WITH CHECK (
    tenant_id = current_setting('app.tenant_id')::bigint
);

COMMIT;

-- drop function if exists authenticate_user(text)

-- CREATE OR REPLACE FUNCTION public.authenticate_user(p_email TEXT)
-- RETURNS TABLE (
--     organization TEXT,
--     organization_legal_name TEXT,
--     created_by TEXT,
--     id BIGINT,
--     role SMALLINT,
--     last_login TIMESTAMP,
--     last_login_local TIMESTAMP,
--     lastname TEXT,
--     surname TEXT,
--     tenant_id BIGINT,
--     uuid UUID,
--     name TEXT,
--     email TEXT,
--     phone TEXT,
--     active SMALLINT,
--     blocked SMALLINT,
--     blocked_by BIGINT,
--     password TEXT,
--     username TEXT,
--     locale TEXT,
--     tax_id TEXT,
--     avatar TEXT,
--     roles varchar[],
--     permissions varchar[]
-- )
-- LANGUAGE sql
-- SECURITY DEFINER
-- SET search_path = public
-- AS $$
--     SELECT
--         b.trade_name AS organization,
--         b.legal_name AS organization_legal_name,

--         (
--             SELECT cu.name
--             FROM public.users cu
--             WHERE cu.id = u.created_by
--             LIMIT 1
--         ) AS created_by,

--         u.id,
--         u.role,
--         u.last_login,
--         u.last_login_local,
--         u.lastname,
--         u.surname,
--         u.tenant_id,
--         u.uuid,
--         u.name,
--         u.email,
--         u.phone,
--         u.active,
--         u.blocked,
--         u.blocked_by,
--         u.password,
--         un.username,
--         u.locale,
--         b.tax_id,
--         u.avatar,
--         u.roles,
--         u.permissions

--     FROM public.users u

--     INNER JOIN public.tenants t
--         ON u.tenant_id = t.id

--     INNER JOIN public.usernames un
--         ON un.user_id = u.id

--     INNER JOIN public.business b
--         ON b.tenant_id = t.id

--     LEFT JOIN public.business_branding bb
--         ON bb.business_id = b.id

--     WHERE u.email = p_email;
-- $$;