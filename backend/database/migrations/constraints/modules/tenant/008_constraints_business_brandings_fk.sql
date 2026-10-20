
ALTER TABLE "business_brandings" ADD CONSTRAINT "fk_business_branding"  FOREIGN KEY ("business_id") REFERENCES "business" ("id") ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED ;