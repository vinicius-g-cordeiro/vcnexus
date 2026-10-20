
ALTER TABLE "addresses" ADD CONSTRAINT "fk_addresses_ownable_types"  FOREIGN KEY ("owner_type_id") REFERENCES "ownable_types" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;