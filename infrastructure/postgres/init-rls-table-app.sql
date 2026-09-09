ALTER TABLE public.users
    ENABLE ROW LEVEL SECURITY;

ALTER TABLE public.users
    FORCE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation
ON public.users
AS RESTRICTIVE
FOR ALL
TO app_user
USING (
    tenant_id = current_setting('app.tenant_id')::bigint
)
WITH CHECK (
    tenant_id = current_setting('app.tenant_id')::bigint
);