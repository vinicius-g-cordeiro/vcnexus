#!/bin/bash
set -e

APP_PASSWORD="$(cat /run/secrets/db_app_password)"

psql \
--username "$POSTGRES_USER" \
--dbname "$POSTGRES_DB" \
-v ON_ERROR_STOP=1 \
-c "CREATE ROLE app_user
        LOGIN
        NOSUPERUSER
        NOCREATEDB
        NOCREATEROLE
        NOINHERIT
        NOBYPASSRLS
PASSWORD '$APP_PASSWORD';"

psql \
--username "$POSTGRES_USER" \
--dbname "$POSTGRES_DB" \
-v ON_ERROR_STOP=1 \
-c "GRANT CONNECT ON DATABASE app_db TO app_user;"


psql \
--username "$POSTGRES_USER" \
--dbname "$POSTGRES_DB" \
-v ON_ERROR_STOP=1 \
-c "GRANT USAGE, CREATE ON SCHEMA public TO app_user;"


psql \
--username "$POSTGRES_USER" \
--dbname "$POSTGRES_DB" \
-v ON_ERROR_STOP=1 \
-c "GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO app_user;"


psql \
--username "$POSTGRES_USER" \
--dbname "$POSTGRES_DB" \
-v ON_ERROR_STOP=1 \
-c "GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO app_user;"

psql \
--username "$POSTGRES_USER" \
--dbname "$POSTGRES_DB" \
-v ON_ERROR_STOP=1 \
-c "ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO app_user;"

psql \
--username "$POSTGRES_USER" \
--dbname "$POSTGRES_DB" \
-v ON_ERROR_STOP=1 \
-c "ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT USAGE, SELECT ON SEQUENCES TO app_user;"

psql \
--username "$POSTGRES_USER" \
--dbname "$POSTGRES_DB" \
-v ON_ERROR_STOP=1 \
-c "CREATE EXTENSION IF NOT EXISTS unaccent;"
