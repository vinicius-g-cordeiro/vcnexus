CREATE TABLE role_permissions (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    role_id bigint NOT NULL,
    permission_id bigint NOT NULL
);
COMMENT ON COLUMN "role_permissions"."uuid" IS 'V7 UUID';