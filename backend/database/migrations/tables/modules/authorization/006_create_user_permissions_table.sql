CREATE TABLE user_permissions (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    permission_id bigint NOT NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp,
    created_by bigint,
    updated_by bigint
);
COMMENT ON COLUMN "user_permissions"."uuid" IS 'V7 UUID';