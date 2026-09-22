# VCNexus ERP — Scrum Epic, Sprint History & Acceptance Criteria

> **Project:** VCNexus ERP
> **Epic:** VCN-EPIC-001 — Build VCNexus ERP
> **Architecture:** Modular Monolith
> **Status:** Planned / Blueprint
> **Development Model:** Scrum
> **Primary Goal:** Build a maintainable multi-tenant ERP as a single deployable application with clear internal module boundaries.

---

# 1. Epic

## VCN-EPIC-001 — Build VCNexus ERP

### Description

Build VCNexus, a multi-tenant ERP system implemented as a **modular monolith**.

The application will remain a single deployable system while separating business capabilities into internally cohesive modules.

Each module owns its business logic and persistence responsibilities while shared infrastructure provides common technical capabilities.

The architecture is intended to prevent the application from becoming tightly coupled while avoiding the operational complexity of a distributed microservices architecture.

### Primary Modules

* Authentication
* Tenant Management
* People
* CRM
* Sales
* Purchasing
* Inventory
* Finance
* Projects
* Human Resources
* Documents
* Reports
* Administration

### Shared Services

* Audit
* Notifications
* Approvals
* Files
* Events
* Authentication
* Tenant Context
* Permissions

---

# 2. Product Goal

The product should provide:

* Multi-tenant ERP functionality.
* Database-level tenant isolation.
* Clearly separated business modules.
* Centralized shared infrastructure.
* Transaction-safe business operations.
* Repository-based persistence.
* PostgreSQL Row-Level Security.
* Vue.js frontend organized around the same business modules.
* Realtime capabilities where required.
* A foundation for future Brazilian fiscal integrations.

---

# 3. Epic Definition of Done

The epic is complete when:

* [ ] The application can be deployed as a complete system.
* [ ] Docker infrastructure is operational.
* [ ] PHP 8.4 backend is operational.
* [ ] Vue.js frontend is operational.
* [ ] PostgreSQL is operational.
* [ ] Redis is operational.
* [ ] ADOdb persistence is operational.
* [ ] HTTP routing is operational.
* [ ] Middleware is operational.
* [ ] Dependency injection is operational.
* [ ] Authentication is operational.
* [ ] Authorization is operational.
* [ ] Multi-tenancy is operational.
* [ ] PostgreSQL RLS protects tenant-scoped data.
* [ ] Database schema workflow is operational.
* [ ] Database migrations are operational.
* [ ] Shared services are operational.
* [ ] Core ERP modules are operational.
* [ ] Cross-module business operations are transactional.
* [ ] Frontend modules are integrated with backend modules.
* [ ] Realtime infrastructure is available.
* [ ] Brazilian fiscal functionality has a defined implementation path.

---

# 4. Sprint History

---

# Sprint 0 — Product & Architecture Definition

## Sprint Goal

Define the product scope, architecture, technology stack, module boundaries, and development strategy before implementation begins.

## Sprint Backlog

### VCN-001 — Define Product Scope

Document the ERP scope and primary business capabilities.

### VCN-002 — Define Modular Monolith Architecture

Establish the internal architecture and module boundaries.

### VCN-003 — Define Technology Stack

Document the technologies and their intended responsibilities.

## Acceptance Criteria

### VCN-001

* [x] The purpose of VCNexus is documented.
* [x] Primary ERP modules are identified.
* [x] Shared services are identified.
* [x] Initial development phases are defined.
* [x] The initial project scope is documented.

### VCN-002

* [x] Modular Monolith is explicitly selected as the architecture.
* [x] Backend module boundaries are defined.
* [x] Shared infrastructure boundaries are defined.
* [x] Module responsibilities are documented.
* [x] The application remains conceptually one deployable system.
* [x] Microservices are not required for the initial architecture.

### VCN-003

* [x] PHP 8.4 is defined as the backend runtime.
* [x] Vue.js 3 is defined as the frontend framework.
* [x] PostgreSQL is defined as the primary database.
* [x] Redis is defined as the caching/supporting data store.
* [x] ADOdb is defined as the database abstraction layer.
* [x] Docker is defined as the development/deployment infrastructure.

## Sprint Definition of Done

* [x] Product scope documented.
* [x] Architecture documented.
* [x] Technology stack documented.
* [x] Initial module list documented.
* [x] Development phases documented.

---

# Sprint 1 — Application Platform

## Sprint Goal

Create the basic executable application and its infrastructure.

## Sprint Backlog

### VCN-010 — Docker Environment

Create the initial containerized development environment.

### VCN-011 — PHP Application Bootstrap

Create the PHP application entry point and bootstrap process.

### VCN-012 — HTTP Router

Implement HTTP routing and controller dispatch.

### VCN-013 — Middleware Pipeline

Implement the middleware execution pipeline.

### VCN-014 — Dependency Injection

Establish dependency registration and resolution.

## Acceptance Criteria

### Docker

* [ ] PHP 8.4 container starts successfully.
* [ ] Nginx starts successfully.
* [ ] PostgreSQL starts successfully.
* [ ] Redis starts successfully.
* [ ] Containers can communicate through the Docker network.
* [ ] Application configuration is supplied through environment configuration.
* [ ] Secrets are not committed to the repository.

### PHP Bootstrap

* [ ] `public/index.php` is a thin entry point.
* [ ] Application initialization occurs through `Bootstrap/Application.php`.
* [ ] Configuration is loaded before application services are initialized.
* [ ] Dependencies are initialized during application bootstrap.
* [ ] An HTTP request can reach the application.

### Router

* [ ] Routes can be registered.
* [ ] HTTP methods are recognized.
* [ ] Route parameters are supported.
* [ ] Controllers can be resolved.
* [ ] Unknown routes return an appropriate HTTP response.

### Middleware

* [ ] Middleware can be registered.
* [ ] Middleware executes in a deterministic order.
* [ ] Middleware can stop request processing.
* [ ] Middleware can pass control to the next middleware.
* [ ] Controllers execute only after the middleware pipeline completes successfully.

### Dependency Injection

* [ ] Services can be registered.
* [ ] Dependencies can be resolved.
* [ ] Shared dependencies are not manually instantiated throughout controllers.
* [ ] Application bootstrap owns the initial dependency configuration.

## Sprint Definition of Done

* [ ] Docker environment starts successfully.
* [ ] A basic HTTP endpoint responds.
* [ ] Routing works.
* [ ] Middleware works.
* [ ] Dependency injection works.
* [ ] Application bootstrap is functional.

---

# Sprint 2 — Database & Persistence

## Sprint Goal

Establish the database connection, ADOdb integration, repository layer, and transaction handling.

## Sprint Backlog

### VCN-020 — PostgreSQL Configuration

Configure the PostgreSQL environment.

### VCN-021 — ADOdb Integration

Create the application database abstraction layer.

### VCN-022 — Repository Layer

Create the repository pattern for persistence.

### VCN-023 — Transaction Management

Establish service-owned transactions.

## Acceptance Criteria

### PostgreSQL

* [ ] PostgreSQL starts through Docker.
* [ ] Application database is created.
* [ ] Application database user is created.
* [ ] Database credentials are supplied securely.
* [ ] Application can establish a database connection.

### ADOdb

* [ ] ADOdb is available to the application.
* [ ] PostgreSQL connections can be established through ADOdb.
* [ ] Connection configuration is centralized.
* [ ] Database errors can be propagated to the application.
* [ ] Transactions can be started through ADOdb.

### Repositories

* [ ] Repositories provide persistence operations.
* [ ] Repositories use ADOdb rather than controllers directly accessing the database.
* [ ] Database-specific implementation details remain inside the persistence layer.
* [ ] Services can consume repositories through dependency injection.

### Transactions

* [ ] Services can start transactions.
* [ ] Successful operations commit.
* [ ] Failed operations roll back.
* [ ] Exceptions are propagated after rollback.
* [ ] Multiple repository operations can participate in the same transaction.

## Sprint Definition of Done

* [ ] Application connects to PostgreSQL.
* [ ] ADOdb is operational.
* [ ] Repository persistence works.
* [ ] Transactions commit correctly.
* [ ] Transactions roll back correctly.

---

# Sprint 3 — Schema & Migration System

## Sprint Goal

Establish the database schema as a controlled source of truth and create the migration workflow.

## Sprint Backlog

### VCN-030 — Schema Source of Truth

Establish the `schemas/` directory as the authoritative schema definition.

### VCN-031 — Schema Attributes

Implement reusable schema metadata attributes.

### VCN-032 — Schema Compiler

Generate database-related artifacts from schema definitions.

### VCN-033 — Migration System

Create the global database migration workflow.

## Acceptance Criteria

### Schema

* [ ] Schema definitions are stored under `schemas/`.
* [ ] Schema definitions describe database structures.
* [ ] Schema changes can be represented without manually duplicating definitions across multiple locations.

### Attributes

The schema system supports the required metadata concepts:

* [ ] `Column`
* [ ] `PrimaryKey`
* [ ] `ForeignKey`
* [ ] `Index`
* [ ] `Unique`
* [ ] `Nullable`
* [ ] `Timestamps`
* [ ] `TenantScoped`

### Compiler

* [ ] Schema definitions can be processed.
* [ ] Models can be generated.
* [ ] DTOs can be generated.
* [ ] Migrations can be generated.
* [ ] Generated artifacts are deterministic.
* [ ] Generated files follow the project's directory conventions.

### Migrations

* [ ] Migrations are stored under `database/migrations/`.
* [ ] Migrations have an ordered execution sequence.
* [ ] Migration state can be tracked.
* [ ] Migrations can be applied to a new database.
* [ ] Migration failures are reported.

## Sprint Definition of Done

* [ ] Schema source of truth exists.
* [ ] Schema attributes work.
* [ ] Compiler workflow exists.
* [ ] Generated migrations can initialize the database.

---

# Sprint 4 — Multi-Tenancy

## Sprint Goal

Implement tenant management and database-enforced tenant isolation.

## Sprint Backlog

### VCN-040 — Tenant Module

Create tenant management functionality.

### VCN-041 — Tenant Context

Establish the active tenant during a request.

### VCN-042 — PostgreSQL RLS

Implement database-level tenant isolation.

## Acceptance Criteria

### Tenant Module

* [ ] Tenants can be created.
* [ ] Tenants can be retrieved.
* [ ] Tenant information can be updated.
* [ ] Tenant identity can be resolved.
* [ ] Tenant-specific configuration can be stored.

### Tenant Context

* [ ] The authenticated request can identify its tenant.
* [ ] Tenant context is available to application services.
* [ ] Tenant context is established before tenant-scoped database operations.
* [ ] Tenant context is not silently inherited from an unrelated request.
* [ ] Missing tenant context is handled explicitly.

### PostgreSQL RLS

* [ ] Tenant-scoped tables have tenant identification.
* [ ] RLS is enabled where required.
* [ ] Tenant policies restrict access to the active tenant.
* [ ] Queries cannot access another tenant's records through normal application access.
* [ ] Inserts cannot create records for unauthorized tenants.
* [ ] Updates cannot modify records belonging to another tenant.
* [ ] Deletes cannot remove records belonging to another tenant.

## Sprint Definition of Done

* [ ] Tenant creation works.
* [ ] Tenant context works.
* [ ] RLS policies work.
* [ ] Cross-tenant access is rejected.

---

# Sprint 5 — Authentication & Authorization

## Sprint Goal

Implement user authentication, sessions, roles, and permissions.

## Sprint Backlog

### VCN-050 — Authentication

Implement login, logout, session handling, and current-user functionality.

### VCN-051 — Roles

Implement role definitions and assignments.

### VCN-052 — Permissions

Implement permission management.

### VCN-053 — Authorization Middleware

Enforce permissions at the HTTP boundary.

## Acceptance Criteria

### Authentication

* [ ] Users can authenticate.
* [ ] Invalid credentials are rejected.
* [ ] Authenticated sessions are maintained.
* [ ] Users can log out.
* [ ] The current authenticated user can be retrieved.
* [ ] Unauthenticated requests cannot access protected resources.

### Roles

Initial roles are:

| Value | Role                |
| ----: | ------------------- |
|     1 | Super Administrator |
|     2 | Administrator       |
|     3 | Manager             |
|     4 | Worker              |
|     5 | Viewer              |

* [ ] Roles are represented consistently.
* [ ] Users can have assigned roles.
* [ ] Multiple roles can be handled without duplicate role entries.
* [ ] Role information can be retrieved for an authenticated user.

### Permissions

* [ ] Permissions can be defined.
* [ ] Permissions can be assigned according to the authorization model.
* [ ] Users can be evaluated against required permissions.
* [ ] Unauthorized operations are rejected.

### Middleware

* [ ] Protected routes require authentication.
* [ ] Permission-protected routes require the corresponding permission.
* [ ] Authorization occurs before controller execution.
* [ ] Authorization failures return an appropriate HTTP response.

## Sprint Definition of Done

* [ ] Login works.
* [ ] Logout works.
* [ ] Current-user retrieval works.
* [ ] Roles work.
* [ ] Permissions work.
* [ ] Protected routes reject unauthorized requests.

---

# Sprint 6 — Shared Services

## Sprint Goal

Implement reusable cross-cutting services without moving module-specific business rules into shared code.

## Sprint Backlog

* VCN-060 — Audit
* VCN-061 — Notifications
* VCN-062 — Approvals
* VCN-063 — Files
* VCN-064 — Events

## Acceptance Criteria

### Audit

* [ ] Important system operations can generate audit records.
* [ ] Audit records contain enough information to identify the operation.
* [ ] Audit functionality can be consumed by multiple modules.

### Notifications

* [ ] Modules can request notifications.
* [ ] Notification delivery is separated from module business logic.
* [ ] Notification functionality can be reused by multiple modules.

### Approvals

* [ ] Modules can request approval workflows.
* [ ] Approval state can be tracked.
* [ ] Approval functionality is not coupled to a single ERP module.

### Files

* [ ] Modules can associate files with business records.
* [ ] File metadata can be managed independently of individual business modules.

### Events

* [ ] Modules can publish application events.
* [ ] Event consumers can be registered.
* [ ] Event infrastructure does not require direct coupling between unrelated modules.

## Sprint Definition of Done

* [ ] Shared services are reusable.
* [ ] Shared services do not contain unrelated module-specific business rules.
* [ ] At least one module can consume each required shared service.

---

# Sprint 7 — Frontend Platform

## Sprint Goal

Create the Vue.js application foundation and connect it to the backend.

## Sprint Backlog

### VCN-070 — Vue Application

Create the frontend application.

### VCN-071 — Frontend Module Structure

Establish frontend module boundaries.

### VCN-072 — API Layer

Create reusable HTTP communication.

### VCN-073 — Frontend Authentication

Connect frontend authentication to the backend.

## Acceptance Criteria

### Vue Application

* [ ] Vue.js 3 application starts successfully.
* [ ] Vite development server works.
* [ ] TailwindCSS is configured.
* [ ] Pinia is configured.
* [ ] vue-i18n is configured.

### Modules

* [ ] Frontend modules are organized by business capability.
* [ ] Module-specific services remain inside their modules.
* [ ] Shared UI functionality is kept separate from business modules.

### API Layer

* [ ] HTTP requests use the generic API layer.
* [ ] API errors can be handled consistently.
* [ ] Authentication information can be transmitted.
* [ ] Module services can consume the API layer.

### Authentication

* [ ] Login can be performed through the frontend.
* [ ] Authentication state is stored.
* [ ] Protected frontend routes can be identified.
* [ ] Logout clears the frontend authentication state.

## Sprint Definition of Done

* [ ] Vue application starts.
* [ ] Backend API can be called.
* [ ] Login works through the frontend.
* [ ] Frontend module structure exists.

---

# Sprint 8 — People

## Sprint Goal

Implement the People module as a reusable foundation for other ERP modules.

## Acceptance Criteria

* [ ] People can be created.
* [ ] People can be retrieved.
* [ ] People can be updated.
* [ ] People can be deactivated where applicable.
* [ ] Contact information can be maintained.
* [ ] Address information can be maintained.
* [ ] People records are tenant-scoped where required.
* [ ] RLS prevents cross-tenant access.
* [ ] Backend and frontend functionality are integrated.
* [ ] Validation errors are handled consistently.

## Sprint Definition of Done

* [ ] People CRUD works.
* [ ] Tenant isolation works.
* [ ] Frontend integration works.
* [ ] Validation works.

---

# Sprint 9 — Inventory

## Sprint Goal

Implement inventory and stock management.

## Acceptance Criteria

* [ ] Products can be created.
* [ ] Products can be updated.
* [ ] Products can be deactivated.
* [ ] Warehouses can be managed.
* [ ] Stock quantities can be retrieved.
* [ ] Inventory movements can be recorded.
* [ ] Stock adjustments can be recorded.
* [ ] Inventory history can be retrieved.
* [ ] Inventory data is tenant-scoped.
* [ ] Stock changes are transactional.
* [ ] Invalid stock operations are rejected according to business rules.
* [ ] Frontend inventory screens communicate with the backend.

## Sprint Definition of Done

* [ ] Product management works.
* [ ] Warehouse management works.
* [ ] Stock movement works.
* [ ] Inventory history works.
* [ ] Transactions protect stock changes.

---

# Sprint 10 — Sales

## Sprint Goal

Implement sales and establish the first major cross-module business flow.

## Acceptance Criteria

* [ ] Customers can be selected or created.
* [ ] Sales orders can be created.
* [ ] Sales order items can be added.
* [ ] Sales order status can be managed.
* [ ] Sales orders can be retrieved.
* [ ] Sales can trigger inventory operations.
* [ ] Sales can trigger financial operations.
* [ ] Cross-module operations execute transactionally.
* [ ] A failed inventory operation rolls back the overall transaction.
* [ ] A failed financial operation rolls back the overall transaction.
* [ ] Sales cannot access another tenant's records.
* [ ] Sales frontend functionality is operational.

## Sprint Definition of Done

```text
Sales
  |
  +----> Inventory
  |
  +----> Finance
```

* [ ] Complete sales flow works.
* [ ] Inventory integration works.
* [ ] Finance integration works.
* [ ] Transaction rollback works.
* [ ] Tenant isolation works.

---

# Sprint 11 — Purchasing

## Sprint Goal

Implement purchasing and supplier-related operations.

## Acceptance Criteria

* [ ] Suppliers can be managed.
* [ ] Purchase orders can be created.
* [ ] Purchase items can be managed.
* [ ] Purchase status can be tracked.
* [ ] Receiving can be recorded.
* [ ] Receiving can create inventory movements.
* [ ] Purchasing can create financial obligations.
* [ ] Inventory and finance operations participate in the appropriate transaction.
* [ ] Failed operations roll back the business transaction.
* [ ] Purchasing data is tenant-isolated.
* [ ] Frontend purchasing functionality is operational.

## Sprint Definition of Done

```text
Purchasing
     |
     +----> Inventory
     |
     +----> Finance
```

* [ ] Complete purchasing flow works.
* [ ] Inventory integration works.
* [ ] Finance integration works.
* [ ] Transaction rollback works.

---

# Sprint 12 — Finance

## Sprint Goal

Implement the financial foundation required by sales and purchasing.

## Acceptance Criteria

* [ ] Accounts receivable can be represented.
* [ ] Accounts payable can be represented.
* [ ] Financial transactions can be recorded.
* [ ] Payments can be recorded.
* [ ] Financial categories can be managed.
* [ ] Sales can reference financial records.
* [ ] Purchasing can reference financial records.
* [ ] Financial records are tenant-scoped.
* [ ] Financial operations participate in application transactions.
* [ ] Financial records have sufficient audit information.

## Sprint Definition of Done

* [ ] Receivables work.
* [ ] Payables work.
* [ ] Payments work.
* [ ] Sales integration works.
* [ ] Purchasing integration works.

---

# Sprint 13 — Operations

## Sprint Goal

Implement Projects, Human Resources, and Documents.

---

## Projects

### Acceptance Criteria

* [ ] Projects can be created.
* [ ] Projects can be updated.
* [ ] Tasks can be created.
* [ ] Tasks can be assigned.
* [ ] Project status can be tracked.
* [ ] Project records are tenant-scoped.

---

## Human Resources

### Acceptance Criteria

* [ ] Employee records can be maintained.
* [ ] Employee organizational information can be maintained.
* [ ] Employee records are tenant-scoped.
* [ ] Appropriate permissions are enforced.

---

## Documents

### Acceptance Criteria

* [ ] Document records can be created.
* [ ] Documents can reference files.
* [ ] Document metadata can be maintained.
* [ ] Document access is tenant-aware.
* [ ] Document operations can use the shared Files service.

## Sprint Definition of Done

* [ ] Projects functionality works.
* [ ] HR functionality works.
* [ ] Documents functionality works.
* [ ] Tenant isolation works across all three areas.

---

# Sprint 14 — CRM

## Sprint Goal

Implement customer relationship management.

## Acceptance Criteria

* [ ] Leads can be created.
* [ ] Leads can be updated.
* [ ] Contacts can be managed.
* [ ] Opportunities can be created.
* [ ] Opportunities can progress through defined stages.
* [ ] Customer activities can be recorded.
* [ ] Customer interactions can be tracked.
* [ ] CRM data is tenant-scoped.
* [ ] CRM permissions are enforced.
* [ ] CRM frontend functionality is operational.

## Sprint Definition of Done

* [ ] Lead management works.
* [ ] Contact management works.
* [ ] Opportunity management works.
* [ ] Activity tracking works.
* [ ] Tenant isolation works.

---

# Sprint 15 — Reporting & Management

## Sprint Goal

Provide management dashboards and operational reporting.

## Acceptance Criteria

* [ ] Reports can retrieve information from relevant modules.
* [ ] Reports respect tenant boundaries.
* [ ] Users can only access reports they are authorized to view.
* [ ] Sales reports can be generated.
* [ ] Inventory reports can be generated.
* [ ] Financial reports can be generated.
* [ ] Management dashboards can display relevant indicators.
* [ ] Reporting code does not take ownership of another module's business rules.
* [ ] Report queries are designed to avoid unnecessary application-side aggregation.

## Sprint Definition of Done

* [ ] Core reports work.
* [ ] Dashboards work.
* [ ] Permissions work.
* [ ] Tenant isolation works.

---

# Sprint 16 — Administration

## Sprint Goal

Implement system and tenant administration.

## Acceptance Criteria

* [ ] Users can be administered.
* [ ] Roles can be administered.
* [ ] Permissions can be administered.
* [ ] Tenant configuration can be administered.
* [ ] System configuration can be managed where applicable.
* [ ] Administrative operations are protected by authorization.
* [ ] Administrative actions can generate audit records.
* [ ] Tenant administrators cannot access unauthorized tenant data.
* [ ] Super-administrative functionality follows the defined authorization model.

## Sprint Definition of Done

* [ ] User administration works.
* [ ] Role administration works.
* [ ] Permission administration works.
* [ ] Tenant administration works.
* [ ] Administrative authorization works.
* [ ] Audit integration works.

---

# Sprint 17 — Realtime

## Sprint Goal

Introduce realtime functionality while keeping realtime functionality within the same application codebase.

## Acceptance Criteria

* [ ] Ratchet can start successfully.
* [ ] Realtime functionality uses the same project codebase.
* [ ] Realtime has a dedicated application entry point.
* [ ] HTTP and realtime execution can run independently.
* [ ] Shared application services can be reused.
* [ ] Authentication can be validated for realtime connections.
* [ ] Tenant context can be established for realtime operations.
* [ ] Unauthorized realtime operations are rejected.
* [ ] Realtime functionality does not require duplicating business logic.

## Sprint Definition of Done

```text
VCNexus Codebase
       |
       +----------------------+
       |                      |
       v                      v
HTTP Entry Point       Realtime Entry Point
       |                      |
       v                      v
HTTP Application          Ratchet
       |                      |
       +----------+-----------+
                  |
                  v
          Shared Application
```

* [ ] HTTP continues working independently.
* [ ] Realtime connections work.
* [ ] Shared services are reusable.
* [ ] Authorization and tenant isolation remain enforced.

---

# Sprint 18 — Brazilian Fiscal Support

## Sprint Goal

Prepare VCNexus for Brazilian fiscal operations.

## Acceptance Criteria

### NF-e

* [ ] NF-e domain requirements are documented.
* [ ] NF-e data model is defined.
* [ ] Integration boundary is defined.
* [ ] SEFAZ communication requirements are documented.

### NFS-e

* [ ] NFS-e requirements are documented.
* [ ] Municipal integration requirements are identified.
* [ ] NFS-e domain model is defined.

### NFC-e

* [ ] NFC-e requirements are documented.
* [ ] NFC-e workflow is defined.
* [ ] Integration requirements are documented.

### SEFAZ

* [ ] SEFAZ integration boundary is isolated from core ERP modules.
* [ ] External communication failures can be handled.
* [ ] Fiscal operations can be audited.
* [ ] Fiscal integration credentials are handled securely.

## Sprint Definition of Done

* [ ] Fiscal architecture is documented.
* [ ] Required fiscal domains are defined.
* [ ] External integration boundaries are established.
* [ ] Fiscal functionality does not unnecessarily couple the core ERP modules to external providers.

---

# 5. Cross-Sprint Acceptance Requirements

The following requirements apply throughout development.

## Security

* [ ] Tenant data must not be exposed across tenants.
* [ ] Protected operations require authentication.
* [ ] Authorization must be enforced server-side.
* [ ] Secrets must not be committed.
* [ ] Database access must use the configured application database role.
* [ ] Sensitive operations should be auditable.

## Multi-Tenancy

* [ ] Tenant context must be explicit.
* [ ] Tenant-scoped records must contain tenant identification.
* [ ] PostgreSQL RLS must enforce tenant isolation.
* [ ] Application filtering must not be considered the only tenant-isolation mechanism.

## Transactions

* [ ] Business transactions are coordinated by application services.
* [ ] Related repository operations participate in the same transaction.
* [ ] Failed operations roll back.
* [ ] Partial business operations must not leave inconsistent state.

## Architecture

* [ ] Business modules maintain clear responsibilities.
* [ ] Controllers remain thin.
* [ ] Repositories own persistence.
* [ ] Services coordinate business operations.
* [ ] Shared services remain generic.
* [ ] Modules should not depend on unrelated implementation details from other modules.
* [ ] Infrastructure concerns remain separated from business modules.

---

# 6. Module Dependency Model

Modules may communicate when required by a business process.

```text
                 +-------------+
                 |    Sales    |
                 +------+------+
                        |
             +----------+----------+
             |                     |
             v                     v
      +-------------+       +-------------+
      |  Inventory  |       |   Finance   |
      +-------------+       +-------------+

                 +-------------+
                 | Purchasing  |
                 +------+------+
                        |
             +----------+----------+
             |                     |
             v                     v
      +-------------+       +-------------+
      |  Inventory  |       |   Finance   |
      +-------------+       +-------------+
```

The existence of a dependency does not mean modules should directly access each other's internal implementation.

Business interactions should occur through defined services or application-level contracts.

---

# 7. Scrum Release Plan

## Release 1 — Platform Foundation

### Included

* Docker
* PHP bootstrap
* Routing
* Middleware
* Dependency injection
* PostgreSQL
* ADOdb
* Redis
* Schema system
* Migrations

### Release Acceptance Criteria

* [ ] Application starts.
* [ ] Database works.
* [ ] HTTP pipeline works.
* [ ] Persistence works.
* [ ] Database schema can be created through the defined workflow.

---

# Release 2 — Security & Multi-Tenancy

### Included

* Authentication
* Roles
* Permissions
* Tenant management
* Tenant context
* PostgreSQL RLS
* Shared audit infrastructure

### Release Acceptance Criteria

* [ ] Users can authenticate.
* [ ] Users can be authorized.
* [ ] Tenants can be resolved.
* [ ] Tenant isolation is enforced.
* [ ] Unauthorized operations are rejected.

---

# Release 3 — Core ERP

### Included

* People
* Inventory
* Sales
* Purchasing
* Finance

### Release Acceptance Criteria

* [ ] People management works.
* [ ] Inventory works.
* [ ] Sales works.
* [ ] Purchasing works.
* [ ] Finance works.
* [ ] Sales integrates with Inventory and Finance.
* [ ] Purchasing integrates with Inventory and Finance.
* [ ] Cross-module transactions roll back correctly.

---

# Release 4 — Management & Operations

### Included

* Projects
* HR
* Documents
* CRM
* Reports
* Administration

### Release Acceptance Criteria

* [ ] Operational modules work.
* [ ] CRM works.
* [ ] Reports work.
* [ ] Administration works.
* [ ] Permissions are enforced.
* [ ] Tenant isolation is maintained.

---

# Release 5 — Extended Platform

### Included

* Realtime
* Brazilian fiscal capabilities

### Release Acceptance Criteria

* [ ] Realtime infrastructure works.
* [ ] Realtime authentication works.
* [ ] Realtime tenant isolation works.
* [ ] Fiscal integration boundaries are established.
* [ ] Fiscal operations are auditable.

---

# 8. Final Architecture

```text
                              VCNexus ERP
                                   |
                +------------------+------------------+
                |                                     |
             Frontend                               Backend
                |                                     |
             Vue 3                              Application
                |                                     |
       +--------+--------+                  +---------+---------+
       |        |        |                  |         |         |
     Pinia   i18n    Components           Router   Middleware   DI
       |                                      |         |
       +------------------+-------------------+---------+
                          |
                   Business Modules
                          |
       +------------------+-------------------------------+
       |                  |               |               |
   Authentication       Tenant          People           CRM
       |                  |               |               |
       +------------------+---------------+---------------+
                          |
             +------------+------------+
             |            |            |
           Sales     Purchasing    Inventory
             |            |            |
             +------------+------------+
                          |
                       Finance
                          |
             +------------+------------+
             |            |            |
          Projects       HR       Documents
             |
             +------------------------+
                          |
                    Shared Services
                          |
       +------------------+------------------+
       |          |          |       |       |
      Audit   Notifications Events Files Approvals
                          |
                    Infrastructure
                          |
       +------------------+------------------+
       |                  |                  |
    PostgreSQL          Redis              ADOdb
       |
   PostgreSQL RLS
       |
 Tenant Isolation
```

---

# 9. Final Architectural Principle

The project follows one central rule:

> **Keep one application, but don't let everything depend on everything else.**

The modular monolith should remain simple to deploy while maintaining enough internal separation that individual modules can evolve without turning the application into a tightly coupled codebase.

The goal is not to create artificial complexity around module boundaries.

The goal is to make the boundaries clear enough that complexity remains manageable as VCNexus grows.
