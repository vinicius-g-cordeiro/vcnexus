
ALTER TABLE "chat_messages" ADD CONSTRAINT "fk_chat_messages_room"  FOREIGN KEY ("room_id") REFERENCES "chat_rooms" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;
ALTER TABLE "chat_messages" ADD CONSTRAINT "fk_chat_messages_user"  FOREIGN KEY ("user_id") REFERENCES "user_credentials" ("id") ON UPDATE NO ACTION DEFERRABLE INITIALLY DEFERRED ;