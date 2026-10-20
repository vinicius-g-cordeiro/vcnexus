CREATE TABLE chat_rooms (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    created_at timestamp NOT NULL,
    tenant_id bigint
);
COMMENT ON COLUMN "chat_rooms"."uuid" IS 'V7 UUID';