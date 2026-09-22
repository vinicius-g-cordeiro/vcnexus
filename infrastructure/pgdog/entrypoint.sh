#!/bin/sh
set -eu

# Escape characters that are special in a sed replacement (delimiter |, &, \)
esc() { printf '%s' "$1" | sed -e 's/[\/&|\\]/\\&/g'; }

DB_USERNAME="$(cat /run/secrets/db_username 2>/dev/null || echo "${DB_USERNAME:-app_user}")"
PGDOG_DB_PASSWORD="$(cat /run/secrets/db_app_password)"
PGDOG_ADMIN_PASSWORD="$(cat /run/secrets/pgdog_admin_password)"

# Logical name clients connect to (what PHP sends as the database)
PGDOG_DATABASE_NAME="${PGDOG_DATABASE_NAME:-app}"
# Real Postgres database behind pgdog
PGSQL_DATABASE_NAME="${PGSQL_DATABASE_NAME:-app_db}"

# Escape everything before it goes into sed
DB_USERNAME_ESC="$(esc "$DB_USERNAME")"
PGDOG_DB_PASSWORD_ESC="$(esc "$PGDOG_DB_PASSWORD")"
PGDOG_ADMIN_PASSWORD_ESC="$(esc "$PGDOG_ADMIN_PASSWORD")"
PGDOG_DATABASE_NAME_ESC="$(esc "$PGDOG_DATABASE_NAME")"
PGSQL_DATABASE_NAME_ESC="$(esc "$PGSQL_DATABASE_NAME")"

sed \
  -e "s|__PGDOG_ADMIN_PASSWORD__|${PGDOG_ADMIN_PASSWORD_ESC}|g" \
  -e "s|__PGDOG_DATABASE_NAME__|${PGDOG_DATABASE_NAME_ESC}|g" \
  -e "s|__PGSQL_DATABASE_NAME__|${PGSQL_DATABASE_NAME_ESC}|g" \
  /etc/pgdog/pgdog.toml.tpl > /etc/pgdog/pgdog.toml

sed \
  -e "s|__DB_USERNAME__|${DB_USERNAME_ESC}|g" \
  -e "s|__PGDOG_DB_PASSWORD__|${PGDOG_DB_PASSWORD_ESC}|g" \
  -e "s|__PGDOG_DATABASE_NAME__|${PGDOG_DATABASE_NAME_ESC}|g" \
  /etc/pgdog/users.toml.tpl > /etc/pgdog/users.toml

exec pgdog --config /etc/pgdog/pgdog.toml --users /etc/pgdog/users.toml