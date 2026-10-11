
ALTER TABLE "contacts" ADD CONSTRAINT "fk_contacts_ownable_types"  FOREIGN KEY ("owner_type_id") REFERENCES "ownable_types" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;
ALTER TABLE "contacts" ADD CONSTRAINT "fk_contacts_contact_category"  FOREIGN KEY ("category_id") REFERENCES "contact_categories" ("id") ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED ;
ALTER TABLE "contacts" ADD CONSTRAINT "fk_contacts_contact_type"  FOREIGN KEY ("type_id") REFERENCES "contact_types" ("id") ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED ;