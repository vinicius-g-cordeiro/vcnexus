CREATE TABLE permissions (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(255) NOT NULL,
    description varchar(500),
    slug varchar(100) NOT NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp,
    created_by bigint,
    updated_by bigint,
    tenant_id bigint
);
COMMENT ON COLUMN "permissions"."uuid" IS 'V7 UUID';