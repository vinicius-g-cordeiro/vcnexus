# VCNexus ERP

VCNexus is a multi-tenant ERP built as a **modular monolith** — one PHP application organized into clear business modules (Sales, Inventory, Finance, HR, and more), sharing common infrastructure where it makes sense, without splitting into microservices.

## Tech Stack

| Layer | Choice |
|---|---|
| Runtime | Docker |
| Backend | PHP 8.5 — pure PHP, no framework |
| Database | PostgreSQL, with Row-Level Security for tenant isolation |
| DB Access | ADOdb, pooled through pgDog |
| Cache / Sessions / Queues | Redis |
| Frontend | Vue.js 3, TailwindCSS |
| Realtime | Ratchet (WebSockets) |

## Architecture at a Glance

- **Modular monolith** — one codebase, one deploy, with real boundaries between business modules (`src/Modules/<Name>/`) so any module could be extracted into its own service later if it ever needs to be.
- **Multi-tenant by design** — every tenant-owned table carries a `tenant_id` and is protected by PostgreSQL RLS, not just application-level filtering.
- **No framework, explicit wiring** — routing, middleware, and dependency injection are hand-rolled and live in `src/Bootstrap/` and `src/Shared/`, kept intentionally small.
- **Schema-first** — database tables are defined as PHP schema classes (`src/Schemas/`) using attributes, and a small compiler (`tools/schema-compiler/`) generates migrations and models from them.

## Getting Started

```bash
docker compose down --remove-orphans; docker ps -aq --filter "label=com.docker.compose.project=$(docker compose config --format json | jq -r '.name')" | xargs -r docker rm -f; docker network prune -f && docker ps -a && docker compose --profile dev up --build # to remove the container and it's pesky networks if it happens to exist when rebuilding or restarting the container but do not remove volumes (if doesn't want to build the container everytime, just remove the --build from the last command)

docker compose down --remove-orphans -v; docker ps -aq --filter "label=com.docker.compose.project=$(docker compose config --format json | jq -r '.name')" | xargs -r docker rm -f; docker network prune -f && docker ps -a && docker compose --profile dev up --build # to remove the container and it's pesky networks if it happens to exist when rebuilding or restarting the container removing volumes on the process (if doesn't want to build the container everytime, just remove the --build from the last command)
```

```bash
git clone <repo-url>
cd vcnexus-erp
cp secrets/.env.example secrets/.env   # fill in real values
docker compose --profile dev up --build # first time
docker compose --profile dev up # after building the project for the first time...
docker compose exec php php bin/nexus.php   # first-time only
docker compose exec php composer install && composer dump-autoload -o
```

## Project Structure

```tree
src/
├── Modules/         # Business logic — Sales, Inventory, Finance, Users, Authentication, etc.
├── Shared/           # Cross-cutting: attributes, base classes, the DI container, HTTP layer
├── Infrastructure/    # Database, Redis, mail, storage connections
├── Realtime/           # WebSocket server (Ratchet)
└── Bootstrap/           # Application entry points, routing

frontend/            # Vue 3 app
schemas/              # PHP schema classes — source of truth for the database
tools/                 # Schema compiler
database/migrations/    # Generated SQL, applied by bin/db-migrate.php
docker/                  # Per-service Dockerfiles and configs
secrets/                  # Never committed — see secrets/.env.example
```

## Documentation

Full architecture notes, conventions, and design decisions live in [`docs/`](docs/):

| Doc | Covers |
|---|---|
| [Overview & Architecture](docs/00-overview-architecture.md) | Tech stack, high-level architecture |
| [Project & Module Structure](docs/01-project-structure.md) | Full folder layout, schemas, attributes, secrets |
| [Backend Conventions](docs/02-backend-conventions.md) | Layering, transactions, DI, RBAC, DB infrastructure |
| [Multi-Tenancy & Connection Pooling](docs/03-multi-tenancy.md) | RLS, tenant resolution, pgDog |
| [Business Modules](docs/04-business-modules.md) | Scope of each module |
| [Frontend Architecture](docs/05-frontend-architecture.md) | Vue app structure |
| [Request Flow & Realtime](docs/06-request-flow-realtime.md) | HTTP pipeline, WebSockets |
| [Worked Examples](docs/07-worked-examples.md) | End-to-end Sales/Purchasing flows |
| [Fiscal Module (Future)](docs/08-fiscal-module.md) | Planned NF-e/NFS-e/NFC-e scope |
| [Roadmap](docs/09-roadmap.md) | Build order, first steps |

## Status

Early development. See the [Roadmap](docs/09-roadmap.md) for what's built and what's planned.
