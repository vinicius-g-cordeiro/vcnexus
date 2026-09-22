
ALTER TABLE "user_address" ADD CONSTRAINT "fk_user_address_user_credentials"  FOREIGN KEY ("user_id") REFERENCES "user_credentials" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;