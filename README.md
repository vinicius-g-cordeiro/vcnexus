# VCNexus

VCNexus is a multi-tenant ERP application for managing organizations, users, authentication, tenant data, and operational dashboards.

## Index

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Technology Stack](#technology-stack)
4. [Project Structure](#project-structure)
5. [Requirements](#requirements)
6. [Configuration](#configuration)
7. [Running with Docker](#running-with-docker)
8. [Running Locally](#running-locally)
9. [Application Areas](#application-areas)
10. [API Overview](#api-overview)
11. [Testing and Code Quality](#testing-and-code-quality)
12. [License](#license)

## Overview

VCNexus is organized as a separate backend and frontend:

- The backend provides the HTTP API, authentication, authorization, tenant context, persistence, validation, events, and file handling.
- The frontend provides the Vue single-page application for authentication, dashboards, tenant administration, user administration, and profile management.
- Docker Compose supplies PostgreSQL, Redis, Memcached, PHP-FPM, Nginx, and optional frontend development or build services.

The application is designed for multiple organizations to share one deployment while keeping tenant data isolated through PostgreSQL Row-Level Security and request-level tenant context.

## Architecture

### Backend

The backend is a custom PHP application with:

- PHP attribute-based route discovery through `backend/app/Shared/Router.php`.
- Controllers, services, models, DTOs, and shared request/response helpers.
- Middleware for authentication, guests, tenant resolution, database context, roles, CORS, logging, and rate limiting.
- PostgreSQL schema attributes and initialization scripts, including Row-Level Security policies.
- Event and listener classes for user and tenant lifecycle actions.
- Centralized exception and JSON response handling.

### Frontend

The frontend is a Vue 3 SPA using:

- Vue Router for public and authenticated routes.
- Pinia stores for authentication, tenant, and user state.
- Axios services for API communication.
- Vue I18n translations for English and Brazilian Portuguese.
- Tailwind CSS, Bootstrap, Bootstrap Icons, and Lucide icons for the interface.

## Technology Stack

| Area | Technologies |
| --- | --- |
| Backend | PHP 8.1+, PostgreSQL, ADOdb, Doctrine ORM, Symfony Validator, PHPMailer, PhpSpreadsheet |
| Frontend | Vue 3, Vite, Pinia, Vue Router, Axios, Tailwind CSS, Bootstrap |
| Infrastructure | Docker, Docker Compose, Nginx, PHP-FPM |
| Supporting services | Redis and Memcached |
| Quality tools | PHPUnit and PHP_CodeSniffer |

## Project Structure

```text
vcnexus/
├── backend/
│   ├── app/
│   │   ├── Controller/       # Authentication, tenant, user, and base controllers
│   │   ├── Database/         # Schema attributes, compilers, and database definitions
│   │   ├── DTOs/             # Typed request data transfer objects
│   │   ├── Events/           # Events, listeners, and event container
│   │   ├── Exceptions/       # Application exception handlers
│   │   ├── Middleware/       # Request authentication, tenant, role, and utility middleware
│   │   ├── Model/            # Persistence models
│   │   ├── Service/          # Application and business logic
│   │   └── Shared/           # Router, requests, responses, sessions, context, and helpers
│   ├── config/
│   ├── public/               # Web entry point
│   ├── storage/              # Runtime uploads, logs, and application assets
│   ├── tests/
│   └── composer.json
├── frontend/
│   ├── src/
│   │   ├── components/       # Reusable and feature components
│   │   ├── routes/           # Vue Router definitions and guards
│   │   ├── services/         # API clients
│   │   ├── stores/           # Pinia stores
│   │   └── views/            # Page-level views
│   └── package.json
├── infrastructure/
│   ├── nginx/                # Reverse proxy configuration
│   ├── node/                 # Frontend container image
│   ├── php/                  # PHP-FPM image and PHP configuration
│   └── postgres/             # User and Row-Level Security initialization
├── docker-compose.yml
└── readme.md
```

## Requirements

For the Docker workflow:

- Docker
- Docker Compose

For local development:

- PHP 8.1 or newer
- Composer
- Node.js and npm
- PostgreSQL, Redis, and Memcached

## Configuration

Application configuration is read from environment files. Do not commit credentials or replace secret placeholders with real values in documentation.

The Docker Compose file expects secret files in `secrets/`, including:

- `db_root_password`
- `db_password`
- `db_app_password`
- `redis_password`
- `jwt_secret`
- `app_key`
- `admin_password`
- `smtp_user`
- `smtp_password`
- `vite_api_key`

Create the files before starting Docker services. The application-level PHP configuration is stored in `infrastructure/php/.env`. The root `.env` controls Compose values such as database connection settings and exposed ports.

Common local endpoints are:

- Frontend development server: `http://localhost:5173`
- API through Nginx: `http://localhost:80`
- PostgreSQL: `localhost:5432`
- Redis: `localhost:6379`
- Memcached: `localhost:11211`

## Running with Docker

Clone the repository and enter the project directory:

```bash
git clone https://github.com/vinicius-g-cordeiro/vcnexus.git
cd vcnexus
```

Start the backend and supporting services:

```bash
docker compose up --build
```

Start the frontend development profile as well:

```bash
docker compose --profile dev up --build
```

Build the frontend through the dedicated build profile:

```bash
docker compose --profile build run --rm node-build
```

The first database startup runs the scripts in `infrastructure/postgres/`. Database initialization scripts generally apply only when the PostgreSQL volume is created for the first time.

## Running Locally

Install backend dependencies:

```bash
cd backend
composer install
```

Install and start the frontend:

```bash
cd frontend
npm install
npm run dev
```

Create a production frontend bundle with:

```bash
npm run build
```

When running outside Docker, configure the backend environment and ensure the frontend API base URL points to the accessible API host.

## Application Areas

The current application includes:

- Public home, login, and registration pages.
- Session-based authentication, logout, current-user lookup, and profile updates.
- Authenticated dashboard and profile pages.
- Super-admin tenant listing, creation, viewing, and editing.
- Authenticated user listing, creation, viewing, editing, and profile image upload.
- Tenant-aware request and database context handling.
- Role checks for administrators, owners, and super administrators.
- Request rate limits on sensitive authentication and user operations.
- English and Brazilian Portuguese localization.
- Tenant branding and organization information fields.

## API Overview

Routes are declared with PHP attributes on controllers. The main API groups are:

| Area | Examples |
| --- | --- |
| Authentication | `POST /auth/register/`, `POST /auth/login/`, `GET /auth/me/`, `PUT /auth/me/`, `POST /auth/logout/` |
| Users | `GET /users/list/`, `GET /users/{uuid}`, `POST /users/create/`, `PUT /users/{uuid}`, `POST /users/{uuid}/avatar` |
| Tenants | `GET /tenants/{uuid}`, `GET /tenants/list`, `POST /tenants/save`, `PUT /tenants/save` |
| System | `GET /health-check`, `GET /init-system` |

Authentication, tenant context, and role requirements vary by route. Nginx forwards PHP requests to PHP-FPM and serves uploaded files through the `/storage/` path.

## Testing and Code Quality

Backend tests are located in `backend/tests/`. Run the available PHPUnit suite from the backend directory:

```bash
cd backend
vendor/bin/phpunit
```

Run PHP_CodeSniffer when checking backend coding standards:

```bash
vendor/bin/phpcs app tests
```

## License

The backend package metadata declares the MIT License. A root `LICENSE` file is not currently present in the repository.
