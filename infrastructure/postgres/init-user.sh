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
