
ALTER TABLE "tenants" ADD CONSTRAINT "fk_tenants_subscription_type"  FOREIGN KEY ("subscription_type_id") REFERENCES "subscription_types" ("id") ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED ;
ALTER TABLE "tenants" ADD CONSTRAINT "fk_tenants_subscription_status"  FOREIGN KEY ("subscription_status_id") REFERENCES "subscription_statuses" ("id") ON DELETE SET NULL DEFERRABLE INITIALLY DEFERRED ;