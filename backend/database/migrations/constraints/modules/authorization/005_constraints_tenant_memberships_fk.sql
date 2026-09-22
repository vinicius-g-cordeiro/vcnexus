ALTER TABLE tenant_memberships ADD CONSTRAINT tenant_membership_unique UNIQUE (tenant_id, user_id);

ALTER TABLE "tenant_memberships" ADD CONSTRAINT "fk_user_address_user_credentials"  FOREIGN KEY ("user_id") REFERENCES "user_credentials" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;