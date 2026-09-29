CREATE INDEX IF NOT EXISTS "idx_chat_room_users_room_id" ON "chat_room_users" ("room_id");
ALTER TABLE chat_room_users ADD PRIMARY KEY (user_id, room_id);