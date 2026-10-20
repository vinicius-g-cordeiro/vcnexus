CREATE TABLE tenant_memberships (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    role_id bigint NOT NULL,
    joined_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp,
    deleted_at timestamp,
    created_by bigint,
    updated_by bigint,
    deleted_by bigint,
    deleted_reason varchar(500),
    tenant_id bigint
);
ALTER TABLE tenant_memberships FORCE ROW LEVEL SECURITY;
ALTER TABLE tenant_memberships ENABLE ROW LEVEL SECURITY;
CREATE POLICY tenant_memberships_access ON public.tenant_memberships AS PERMISSIVE FOR SELECT TO app_user USING ( '1' = ANY(current_setting('app.roles', true)::varchar[]) OR user_id = NULLIF(current_setting('app.user_id', true), '')::bigint ) ;
CREATE POLICY tenant_tenant_memberships_isolation ON public.tenant_memberships AS RESTRICTIVE FOR ALL TO app_user USING ( '1' = ANY(current_setting('app.roles', true)::varchar[]) OR tenant_id = NULLIF(current_setting('app.tenant_id', true), '')::bigint ) WITH CHECK ( '1' = ANY(current_setting('app.roles', true)::varchar[]) OR tenant_id = NULLIF(current_setting('app.tenant_id', true), '')::bigint ) ;
CREATE POLICY tenant_tenant_memberships_permissive_isolation ON public.tenant_memberships AS PERMISSIVE FOR ALL TO app_user USING ( '1' = ANY(current_setting('app.roles', true)::varchar[]) OR tenant_id = NULLIF(current_setting('app.tenant_id', true), '')::bigint ) WITH CHECK ( '1' = ANY(current_setting('app.roles', true)::varchar[]) OR tenant_id = NULLIF(current_setting('app.tenant_id', true), '')::bigint ) ;
COMMENT ON COLUMN "tenant_memberships"."uuid" IS 'V7 UUID';