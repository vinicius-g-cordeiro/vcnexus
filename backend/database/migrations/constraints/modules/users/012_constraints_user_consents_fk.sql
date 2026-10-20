
ALTER TABLE "user_consents" ADD CONSTRAINT "fk_user_consents_user_credentials"  FOREIGN KEY ("user_id") REFERENCES "user_credentials" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;