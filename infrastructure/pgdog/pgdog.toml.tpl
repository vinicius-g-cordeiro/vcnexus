[general]
host = "0.0.0.0"
port = 6432

[admin]
name = "admin"
user = "admin"
password = "__PGDOG_ADMIN_PASSWORD__"

[[databases]]
name = "__PGDOG_DATABASE_NAME__"
host = "db"
port = 5432
database_name = "__PGSQL_DATABASE_NAME__"
