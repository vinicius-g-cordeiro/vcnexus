
ALTER TABLE "user_profile" ADD CONSTRAINT "fk_user_profile_user_credentials"  FOREIGN KEY ("user_id") REFERENCES "user_credentials" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;