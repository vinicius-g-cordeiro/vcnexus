CREATE TABLE roles (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(255) NOT NULL,
    description varchar(255) NOT NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp,
    deleted_at timestamp,
    created_by bigint,
    updated_by bigint,
    tenant_id bigint
);
ALTER TABLE roles FORCE ROW LEVEL SECURITY;
ALTER TABLE roles ENABLE ROW LEVEL SECURITY;
CREATE POLICY tenant_roles_isolation ON public.roles AS RESTRICTIVE FOR ALL TO app_user USING ( '1' = ANY(current_setting('app.roles', true)::varchar[]) OR tenant_id = NULLIF(current_setting('app.tenant_id', true), '')::bigint ) WITH CHECK ( '1' = ANY(current_setting('app.roles', true)::varchar[]) OR tenant_id = NULLIF(current_setting('app.tenant_id', true), '')::bigint ) ;
CREATE POLICY tenant_roles_permissive_isolation ON public.roles AS PERMISSIVE FOR ALL TO app_user USING ( '1' = ANY(current_setting('app.roles', true)::varchar[]) OR tenant_id = NULLIF(current_setting('app.tenant_id', true), '')::bigint ) WITH CHECK ( '1' = ANY(current_setting('app.roles', true)::varchar[]) OR tenant_id = NULLIF(current_setting('app.tenant_id', true), '')::bigint ) ;
COMMENT ON COLUMN "roles"."description" IS 'This description will be used for the UI to display to the user''s about the role. It is optional though.';
COMMENT ON COLUMN "roles"."uuid" IS 'V7 UUID';