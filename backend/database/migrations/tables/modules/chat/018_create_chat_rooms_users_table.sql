CREATE TABLE chat_rooms_users (
    id bigint NOT NULL,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint REFERENCES user_credentials (id) ON DELETE CASCADE,
    room_id bigint REFERENCES chat_rooms (id) ON DELETE CASCADE
);
COMMENT ON COLUMN "chat_rooms_users"."uuid" IS 'V7 UUID';