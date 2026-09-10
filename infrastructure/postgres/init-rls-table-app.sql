ALTER TABLE public.tenant_users
    ENABLE ROW LEVEL SECURITY;

ALTER TABLE public.tenant_users
    FORCE ROW LEVEL SECURITY;

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

CREATE POLICY tenant_membership
ON public.tenant_users
AS RESTRICTIVE
FOR SELECT
TO app_user
USING (
    user_id = current_setting('app.user_id')::bigint
);