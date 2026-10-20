CREATE TABLE chat_room_users (
    id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint REFERENCES user_credentials (id) ON DELETE CASCADE,
    room_id bigint REFERENCES chat_rooms (id) ON DELETE CASCADE
);
COMMENT ON COLUMN "chat_room_users"."uuid" IS 'V7 UUID';