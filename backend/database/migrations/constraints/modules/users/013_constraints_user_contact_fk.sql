
ALTER TABLE "user_contact" ADD CONSTRAINT "fk_user_contact_user_credentials"  FOREIGN KEY ("user_id") REFERENCES "user_credentials" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;
ALTER TABLE "user_contact" ADD CONSTRAINT "fk_user_contact_contact_category"  FOREIGN KEY ("category_id") REFERENCES "contact_categories" ("id") ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED ;