
ALTER TABLE "user_contact" ADD CONSTRAINT "fk_user_contact_user_credentials"  FOREIGN KEY ("user_id") REFERENCES "user_credentials" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;