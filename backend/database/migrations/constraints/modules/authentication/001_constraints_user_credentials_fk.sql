ALTER TABLE user_credentials ADD CONSTRAINT uq_users_credentials_email UNIQUE (email);
ALTER TABLE user_credentials ADD CONSTRAINT uq_users_credentials_username UNIQUE (username);
CREATE INDEX IF NOT EXISTS "idx_users_credentials_email" ON "user_credentials" ("email");CREATE INDEX IF NOT EXISTS "idx_users_credentials_username" ON "user_credentials" ("username");