CREATE TABLE tenants (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    subscription_type smallint NOT NULL DEFAULT 1,
    subscription_status smallint NOT NULL DEFAULT 1,
    domain varchar(100),
    slug varchar(100) NOT NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp,
    deleted_at timestamp,
    created_by bigint,
    updated_by bigint,
    deleted_by bigint,
    deleted_reason varchar(500)
);
COMMENT ON COLUMN "tenants"."uuid" IS 'V7 UUID';