CREATE OR REPLACE FUNCTION initialize_system() RETURNS TABLE (
              tenant_id BIGINT,
              business_id BIGINT,
              user_id BIGINT
       ) LANGUAGE plpgsql AS $$
DECLARE v_tenant_id BIGINT;
v_business_id BIGINT;
v_user_id BIGINT;
BEGIN CREATE EXTENSION IF NOT EXISTS pgcrypto;
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS unaccent;
INSERT INTO ownable_types (name)
VALUES ('user_credentials'),
       ('tenants'),
       ('suppliers'),
       ('deliveries'),
       ('orders') ON CONFLICT DO NOTHING;
INSERT INTO subscription_types (name, label, description)
VALUES('free', 'Free', 'Free plan'),
       ('trial', 'Trial', 'Trial plan'),
       ('bronze', 'Bronze', 'Bronze plan'),
       ('silver', 'Silver', 'Silver plan'),
       ('gold', 'Gold', 'Gold plan'),
       ('platinum', 'Platinum', 'Platinum plan'),
       ('diamond', 'Diamond', 'Diamond plan') ON CONFLICT DO NOTHING;
INSERT INTO subscription_statuses (name, label, description)
VALUES('active', 'Active', 'Active subscription'),
       (
              'suspended',
              'Suspended',
              'Suspended subscription'
       ),
       ('canceled', 'Canceled', 'Canceled subscription'),
       ('expired', 'Expired', 'Expired subscription') ON CONFLICT DO NOTHING;
INSERT INTO roles (
              active,
              name,
              description,
              created_by,
              tenant_id
       )
VALUES (
              1,
              'Super Administrator',
              'System administrator with full access',
              1,
              null
       ),
       (
              1,
              'Administrator',
              'System administrator with limited access',
              1,
              null
       ),
       (
              1,
              'User',
              'System user with limited access',
              1,
              null
       ),
       (
              1,
              'Guest',
              'System guest with limited access',
              1,
              null
       ),
       (
              1,
              'Public',
              'System public with very limited access',
              1,
              null
       ),
       (
              1,
              'Human Resources',
              'Human Resources with access to: workers, documents, contracts, salaries, benefits',
              1,
              1
       ),
       (
              1,
              'Manager',
              'Manager with access to: workers, clients, products, services, deliveries',
              1,
              1
       ),
       (
              1,
              'Employee',
              'Employee with access to: clients,products, services, deliveries',
              1,
              1
       ),
       (
              1,
              'Operator',
              'Operator with access to: products, deliveries',
              1,
              1
       ),
       (
              1,
              'Logistics',
              'Logistics with access to: deliveries',
              1,
              1
       ),
       (
              1,
              'Accountant',
              'Accountant with access to: payments, receipts',
              1,
              1
       ),
       (
              1,
              'Cashier',
              'Cashier with access to: payments, receipts',
              1,
              1
       ) ON CONFLICT DO NOTHING;
INSERT INTO permissions (
              name,
              description,
              active,
              created_by,
              slug,
              tenant_id
       )
VALUES ('List', 'View users', 1, 1, 'users.view', 1),
       (
              'Create',
              'Create users',
              1,
              1,
              'users.create',
              1
       ),
       (
              'Update',
              'Update users',
              1,
              1,
              'users.update',
              1
       ),
       (
              'Delete',
              'Delete users',
              1,
              1,
              'users.delete',
              1
       ),
       ('List', 'View roles', 1, 1, 'roles.view', 1),
       (
              'Create',
              'Create roles',
              1,
              1,
              'roles.create',
              1
       ),
       (
              'Update',
              'Update roles',
              1,
              1,
              'roles.update',
              1
       ),
       (
              'Delete',
              'Delete roles',
              1,
              1,
              'roles.delete',
              1
       ),
       ('List', 'View tenants', 1, 1, 'tenants.view', 1),
       (
              'Create',
              'Create tenants',
              1,
              1,
              'tenants.create',
              1
       ),
       (
              'Update',
              'Update tenants',
              1,
              1,
              'tenants.update',
              1
       ),
       (
              'Delete',
              'Delete tenants',
              1,
              1,
              'tenants.delete',
              1
       ),
       ('List', 'View menus', 1, 1, 'menus.view', 1),
       (
              'Create',
              'Create menus',
              1,
              1,
              'menus.create',
              1
       ),
       (
              'Update',
              'Update menus',
              1,
              1,
              'menus.update',
              1
       ),
       (
              'Delete',
              'Delete menus',
              1,
              1,
              'menus.delete',
              1
       ),
       (
              'List',
              'View business',
              1,
              1,
              'business.view',
              1
       ),
       (
              'Create',
              'Create business',
              1,
              1,
              'business.create',
              1
       ),
       (
              'Update',
              'Update business',
              1,
              1,
              'business.update',
              1
       ),
       (
              'Delete',
              'Delete business',
              1,
              1,
              'business.delete',
              1
       ),
       (
              'List',
              'View permissions',
              1,
              1,
              'permissions.view',
              1
       ),
       (
              'Create',
              'Create permissions',
              1,
              1,
              'permissions.create',
              1
       ),
       (
              'Update',
              'Update permissions',
              1,
              1,
              'permissions.update',
              1
       ),
       (
              'Delete',
              'Delete permissions',
              1,
              1,
              'permissions.delete',
              1
       ),
       (
              'List',
              'View suppliers',
              1,
              1,
              'suppliers.view',
              1
       ),
       (
              'Create',
              'Create suppliers',
              1,
              1,
              'suppliers.create',
              1
       ),
       (
              'Update',
              'Update suppliers',
              1,
              1,
              'suppliers.update',
              1
       ),
       (
              'Delete',
              'Delete suppliers',
              1,
              1,
              'suppliers.delete',
              1
       ),
       (
              'List',
              'View products',
              1,
              1,
              'products.view',
              1
       ),
       (
              'Create',
              'Create products',
              1,
              1,
              'products.create',
              1
       ),
       (
              'Update',
              'Update products',
              1,
              1,
              'products.update',
              1
       ),
       (
              'Delete',
              'Delete products',
              1,
              1,
              'products.delete',
              1
       ),
       (
              'List',
              'View services',
              1,
              1,
              'services.view',
              1
       ),
       (
              'Create',
              'Create services',
              1,
              1,
              'services.create',
              1
       ),
       (
              'Update',
              'Update services',
              1,
              1,
              'services.update',
              1
       ),
       (
              'Delete',
              'Delete services',
              1,
              1,
              'services.delete',
              1
       ),
       ('List', 'View clients', 1, 1, 'clients.view', 1),
       (
              'Create',
              'Create clients',
              1,
              1,
              'clients.create',
              1
       ),
       (
              'Update',
              'Update clients',
              1,
              1,
              'clients.update',
              1
       ),
       (
              'Delete',
              'Delete clients',
              1,
              1,
              'clients.delete',
              1
       ),
       (
              'List',
              'View contracts',
              1,
              1,
              'contracts.view',
              1
       ),
       (
              'Create',
              'Create contracts',
              1,
              1,
              'contracts.create',
              1
       ),
       (
              'Update',
              'Update contracts',
              1,
              1,
              'contracts.update',
              1
       ),
       (
              'Delete',
              'Delete contracts',
              1,
              1,
              'contracts.delete',
              1
       ),
       (
              'List',
              'View deliveries',
              1,
              1,
              'deliveries.view',
              1
       ),
       (
              'Create',
              'Create deliveries',
              1,
              1,
              'deliveries.create',
              1
       ),
       (
              'Update',
              'Update deliveries',
              1,
              1,
              'deliveries.update',
              1
       ),
       (
              'Delete',
              'Delete deliveries',
              1,
              1,
              'deliveries.delete',
              1
       ),
       ('List', 'View orders', 1, 1, 'orders.view', 1),
       (
              'Create',
              'Create orders',
              1,
              1,
              'orders.create',
              1
       ),
       (
              'Update',
              'Update orders',
              1,
              1,
              'orders.update',
              1
       ),
       (
              'Delete',
              'Delete orders',
              1,
              1,
              'orders.delete',
              1
       ),
       (
              'List',
              'View payments',
              1,
              1,
              'payments.view',
              1
       ),
       (
              'Create',
              'Create payments',
              1,
              1,
              'payments.create',
              1
       ),
       (
              'Update',
              'Update payments',
              1,
              1,
              'payments.update',
              1
       ),
       (
              'Delete',
              'Delete payments',
              1,
              1,
              'payments.delete',
              1
       ),
       (
              'List',
              'View receipts',
              1,
              1,
              'receipts.view',
              1
       ),
       (
              'Create',
              'Create receipts',
              1,
              1,
              'receipts.create',
              1
       ),
       (
              'Update',
              'Update receipts',
              1,
              1,
              'receipts.update',
              1
       ),
       (
              'Delete',
              'Delete receipts',
              1,
              1,
              'receipts.delete',
              1
       ),
       ('List', 'View workers', 1, 1, 'workers.view', 1),
       (
              'Create',
              'Create workers',
              1,
              1,
              'workers.create',
              1
       ),
       (
              'Update',
              'Update workers',
              1,
              1,
              'workers.update',
              1
       ),
       (
              'Delete',
              'Delete workers',
              1,
              1,
              'workers.delete',
              1
       ),
       (
              'List',
              'View documents',
              1,
              1,
              'documents.view',
              1
       ),
       (
              'Create',
              'Create documents',
              1,
              1,
              'documents.create',
              1
       ),
       (
              'Update',
              'Update documents',
              1,
              1,
              'documents.update',
              1
       ),
       (
              'Delete',
              'Delete documents',
              1,
              1,
              'documents.delete',
              1
       ),
       (
              'List',
              'View salaries',
              1,
              1,
              'salaries.view',
              1
       ),
       (
              'Create',
              'Create salaries',
              1,
              1,
              'salaries.create',
              1
       ),
       (
              'Update',
              'Update salaries',
              1,
              1,
              'salaries.update',
              1
       ),
       (
              'Delete',
              'Delete salaries',
              1,
              1,
              'salaries.delete',
              1
       ),
       (
              'Authentication',
              'Authentication Me',
              1,
              1,
              'authentication.me',
              1
       ),
       (
              'Authentication',
              'Authentication Logout',
              1,
              1,
              'authentication.logout',
              1
       ) ON CONFLICT DO NOTHING;
INSERT INTO menus (
              "uuid",
              active,
              parent_id,
              "label",
              icon,
              route,
              "order",
              permissions,
              tenant_id
       )
VALUES (
              uuidv7(),
              1,
              null,
              'Dashboard',
              'bi-speedometer2',
              'dashboard',
              0,
              ARRAY ['dashboard.view'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Home',
              'bi-house-fill',
              'home',
              0,
              null,
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Users',
              'bi-person-fill',
              'users',
              0,
              ARRAY ['users.view', 'users.create', 'users.reports', 'users.documents'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Menus',
              'bi-list',
              'menus',
              0,
              ARRAY ['menus.view', 'menus.create', 'menus.update', 'menus.delete'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Roles',
              'bi-people-fill',
              'roles',
              0,
              ARRAY ['roles.view', 'roles.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Tenants',
              'bi-building',
              'tenants',
              0,
              ARRAY ['tenants.view', 'tenants.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Permissions',
              'bi-shield-lock-fill',
              'permissions',
              0,
              ARRAY ['permissions.view', 'permissions.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Business',
              'bi-building',
              'business',
              0,
              ARRAY ['business.view', 'business.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Suppliers',
              'bi-truck',
              'suppliers',
              0,
              ARRAY ['suppliers.view', 'suppliers.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Products',
              'bi-box-seam',
              'products',
              0,
              ARRAY ['products.view', 'products.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Deliveries',
              'bi-truck',
              'deliveries',
              0,
              ARRAY ['deliveries.view', 'deliveries.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Orders',
              'bi-basket-fill',
              'orders',
              0,
              ARRAY ['orders.view', 'orders.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Payments',
              'bi-cash-stack',
              'payments',
              0,
              ARRAY ['payments.view', 'payments.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Receipts',
              'bi-receipt-cutoff',
              'receipts',
              0,
              ARRAY ['receipts.view', 'receipts.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Workers',
              'bi-people-fill',
              'workers',
              0,
              ARRAY ['workers.view', 'workers.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Documents',
              'bi-file-earmark-text-fill',
              'documents',
              0,
              ARRAY ['documents.view', 'documents.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Salaries',
              'bi-cash-coin',
              'salaries',
              0,
              ARRAY ['salaries.view', 'salaries.create'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Authentication',
              'bi-shield-lock-fill',
              'authentication',
              0,
              ARRAY ['authentication.me'],
              null
       ),
       (
              uuidv7(),
              1,
              null,
              'Reports',
              'bi-file-earmark-text-fill',
              'reports',
              0,
              ARRAY ['reports.view', 'reports.create'],
              null
       ) ON CONFLICT DO NOTHING;
INSERT INTO menus (
              "uuid",
              active,
              parent_id,
              "label",
              icon,
              route,
              "order",
              permissions,
              tenant_id
       )
VALUES (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'users'
              ),
              'List',
              'bi-person-fill',
              'users.index',
              1,
              ARRAY ['users.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'users'
              ),
              'Create',
              'bi-plus',
              'users.create',
              2,
              ARRAY ['users.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'users'
              ),
              'Reports',
              'bi-file-earmark-text-fill',
              'users.reports',
              3,
              ARRAY ['users.reports'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'users'
              ),
              'Documents',
              'bi-file-earmark-text-fill',
              'users.documents',
              4,
              ARRAY ['users.documents'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'menus'
              ),
              'List',
              'bi-list',
              'menus.index',
              1,
              ARRAY ['menus.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'menus'
              ),
              'Create',
              'bi-plus',
              'menus.create',
              2,
              ARRAY ['menus.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'menus'
              ),
              'Delete',
              'bi-trash',
              'menus.delete',
              4,
              ARRAY ['menus.delete'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'roles'
              ),
              'List',
              'bi-list',
              'roles.index',
              1,
              ARRAY ['roles.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'roles'
              ),
              'Create',
              'bi-plus',
              'roles.create',
              2,
              ARRAY ['roles.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'tenants'
              ),
              'List',
              'bi-list',
              'tenants.index',
              1,
              ARRAY ['tenants.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'tenants'
              ),
              'Create',
              'bi-plus',
              'tenants.create',
              2,
              ARRAY ['tenants.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'permissions'
              ),
              'List',
              'bi-list',
              'permissions.index',
              1,
              ARRAY ['permissions.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'permissions'
              ),
              'Create',
              'bi-plus',
              'permissions.create',
              2,
              ARRAY ['permissions.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'business'
              ),
              'List',
              'bi-list',
              'business.index',
              1,
              ARRAY ['business.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'business'
              ),
              'Create',
              'bi-plus',
              'business.create',
              2,
              ARRAY ['business.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'suppliers'
              ),
              'List',
              'bi-list',
              'suppliers.index',
              1,
              ARRAY ['suppliers.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'suppliers'
              ),
              'Create',
              'bi-plus',
              'suppliers.create',
              2,
              ARRAY ['suppliers.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'products'
              ),
              'List',
              'bi-list',
              'products.index',
              1,
              ARRAY ['products.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'products'
              ),
              'Create',
              'bi-plus',
              'products.create',
              2,
              ARRAY ['products.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'deliveries'
              ),
              'List',
              'bi-list',
              'deliveries.index',
              1,
              ARRAY ['deliveries.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'deliveries'
              ),
              'Create',
              'bi-plus',
              'deliveries.create',
              2,
              ARRAY ['deliveries.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'orders'
              ),
              'List',
              'bi-list',
              'orders.index',
              1,
              ARRAY ['orders.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'orders'
              ),
              'Create',
              'bi-plus',
              'orders.create',
              2,
              ARRAY ['orders.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'payments'
              ),
              'List',
              'bi-list',
              'payments.index',
              1,
              ARRAY ['payments.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'payments'
              ),
              'Create',
              'bi-plus',
              'payments.create',
              2,
              ARRAY ['payments.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'receipts'
              ),
              'List',
              'bi-list',
              'receipts.index',
              1,
              ARRAY ['receipts.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'receipts'
              ),
              'Create',
              'bi-plus',
              'receipts.create',
              2,
              ARRAY ['receipts.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'workers'
              ),
              'List',
              'bi-list',
              'workers.index',
              1,
              ARRAY ['workers.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'workers'
              ),
              'Create',
              'bi-plus',
              'workers.create',
              2,
              ARRAY ['workers.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'documents'
              ),
              'List',
              'bi-file-earmark-text-fill',
              'documents.index',
              1,
              ARRAY ['documents.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'documents'
              ),
              'Create',
              'bi-plus',
              'documents.create',
              2,
              ARRAY ['documents.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'reports'
              ),
              'List',
              'bi-file-earmark-text-fill',
              'reports.index',
              1,
              ARRAY ['reports.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'reports'
              ),
              'Create',
              'bi-plus',
              'reports.create',
              2,
              ARRAY ['reports.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'salaries'
              ),
              'List',
              'bi-cash-coin',
              'salaries.index',
              1,
              ARRAY ['salaries.view'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'salaries'
              ),
              'Create',
              'bi-plus',
              'salaries.create',
              2,
              ARRAY ['salaries.create'],
              null
       ),
       (
              uuidv7(),
              1,
              (
                     SELECT id
                     FROM menus
                     WHERE route = 'authentication'
              ),
              'Authentication',
              'bi-key-fill',
              'authentication.me',
              5,
              ARRAY ['authentication.me', 'authentication.logout'],
              null
       ) ON CONFLICT DO NOTHING;
INSERT INTO tenants (
              domain,
              slug,
              active,
              created_by,
              subscription_type_id,
              subscription_status_id
       )
VALUES (
              'https://demo.vcnexus.com',
              'vcnexus',
              1,
              1,
              7,
              1
       ) ON CONFLICT DO NOTHING
RETURNING id INTO v_tenant_id;
INSERT INTO addresses (
              owner_type_id,
              owner_id,
              purpose,
              address,
              zip_code,
              city_id,
              state_id,
              country_id,
              neighborhood,
              complement,
              reference,
              extra_info
       )
VALUES (
              2,
              v_tenant_id,
              'Home',
              'Fictional Address, Building 1, Apartment 303',
              '72800-000',
              18063,
              378,
              31,
              'Fictional Neighborhood',
              null,
              null,
              null
       ) ON CONFLICT DO NOTHING;
INSERT INTO contacts (
              owner_type_id,
              owner_id,
              type_id,
              value,
              label,
              primary_contact,
              category_id,
              person
       )
VALUES (
              2,
              v_tenant_id,
              2,
              '+55 61 9 9179-5618',
              'WhatsApp',
              1,
              1,
              null
       ) ON CONFLICT DO NOTHING;
INSERT INTO business (
              tenant_id,
              trade_name,
              type,
              tax_id,
              fantasy_name,
              active
       )
VALUES (
              v_tenant_id,
              'VCNexus (MEI)',
              1,
              '000.000.000/0001-01',
              'VCNexus',
              1
       )
RETURNING id INTO v_business_id;
INSERT INTO business_brandings (
              business_id,
              app_name,
              primary_color,
              accent_color,
              text_color,
              background_color,
              font_style,
              button_style
       )
VALUES (
              v_business_id,
              'VCNexus',
              '#2c6693',
              '#7fb44d80',
              '#000000',
              '#FFFFFF',
              'normal',
              'normal'
       );
INSERT INTO user_credentials (email, username, password, active, created_by)
VALUES (
              'vcnexus.vinicius@gmail.com',
              'vcnexus.vinicius',
              '$2a$12$sGVUoPhdf.BF.rOoSjjo/uCJGX0xb1pfALErux3D..764BPxjCbdq',
              1,
              1
       )
RETURNING id INTO v_user_id;
INSERT INTO user_profile (
              user_id,
              firstname,
              surname,
              lastname,
              birthdate,
              locale
       )
VALUES (
              v_user_id,
              'Vinicius',
              'Gonçalves',
              'Cordeiro',
              '1999-04-23',
              'pt-BR'
       );
INSERT INTO user_consents (user_id, purpose, legal_basis, granted_at)
VALUES (
              v_user_id,
              'Diversity reporting',
              'Legal obligation (legitimate interest)',
              NOW()
       );
INSERT INTO user_sensitive (
              user_id,
              socialname,
              gender_id,
              religion_id,
              ethnicity_id,
              sexual_orientation_id,
              disability_id,
              marital_status_id,
              nationality_id,
              updated_at
       )
VALUES (v_user_id, null, 1, 1, 4, 1, null, 1, 32, NOW());
INSERT INTO addresses (
              owner_type_id,
              owner_id,
              purpose,
              address,
              zip_code,
              city_id,
              state_id,
              country_id,
              neighborhood,
              complement,
              reference,
              extra_info
       )
VALUES (
              1,
              v_user_id,
              'Home',
              'Fictional Address, Building 1, Apartment 303',
              '72800-000',
              18063,
              378,
              31,
              'Fictional Neighborhood',
              null,
              null,
              null
       ),
       (
              1,
              v_user_id,
              'Work',
              'Fictional Address, Building 1, Apartment 303',
              '72800-000',
              20990,
              378,
              31,
              'Fictional Neighborhood',
              null,
              null,
              null
       ),
       (
              1,
              v_user_id,
              'Billing',
              'Fictional Address, Building 1, Apartment 313',
              '72810-44',
              20726,
              379,
              31,
              'Taguatinga',
              null,
              null,
              null
       ) ON CONFLICT DO NOTHING;
INSERT INTO contacts (
              owner_type_id,
              owner_id,
              type_id,
              value,
              label,
              primary_contact,
              category_id,
              person
       )
VALUES (
              1,
              v_user_id,
              2,
              '+55 61 9 9179-5618',
              'WhatsApp',
              1,
              1,
              null
       ),
       (
              1,
              v_user_id,
              1,
              'vcnexus.vinicius@gmail.com',
              'Email',
              0,
              1,
              null
       ) ON CONFLICT DO NOTHING;
INSERT INTO tenant_memberships (
              tenant_id,
              user_id,
              role_id,
              created_by,
              active
       )
SELECT v_tenant_id,
       v_user_id,
       id,
       v_user_id,
       1
FROM roles
WHERE name = 'Super Administrator' ON CONFLICT DO NOTHING;
INSERT INTO user_permissions (user_id, permission_id, created_by)
SELECT v_user_id,
       p.id,
       1
FROM permissions p ON CONFLICT DO NOTHING;
INSERT INTO user_roles (user_id, role_id, created_by)
VALUES (v_user_id, 1, 1) ON CONFLICT DO NOTHING;
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id,
       p.id
FROM roles r
       CROSS JOIN permissions p
WHERE r.id = 1 ON CONFLICT DO NOTHING;
INSERT INTO religions (name, description, icon, label)
VALUES ('catholic', 'Catholic', 'bi-church', 'Catholic'),
       (
              'evangelical',
              'Evangelical',
              'bi-church',
              'Evangelical'
       ),
       (
              'protestant',
              'Protestant',
              'bi-church',
              'Protestant'
       ),
       ('muslim', 'Muslim', 'bi-church', 'Muslim'),
       ('buddhist', 'Buddhist', 'bi-church', 'Buddhist'),
       ('jewish', 'Jewish', 'bi-church', 'Jewish'),
       ('hindu', 'Hindu', 'bi-church', 'Hindu'),
       ('sikh', 'Sikh', 'bi-church', 'Sikh'),
       ('atheist', 'Atheist', 'bi-church', 'Atheist'),
       ('agnostic', 'Agnostic', 'bi-church', 'Agnostic'),
       ('other', 'Other', 'bi-church', 'Other');
INSERT INTO completion_statuses (name, description, label, type)
VALUES ('incomplete', 'Incomplete', 'Incomplete', null),
       ('completed', 'Completed', 'Completed', null),
       (
              'in_progress',
              'In progress',
              'In progress',
              null
       ),
       ('on_hold', 'On hold', 'On hold', 1),
       ('dropped_out', 'Dropped out', 'Dropped out', 1),
       ('other', 'Other', 'Other', null),
       (
              'delivery_canceled',
              'Delivery canceled',
              'Delivery canceled',
              3
       ),
       (
              'delivery_pending',
              'Delivery pending',
              'Delivery pending',
              3
       ),
       ('task_missed', 'Task missed', 'Task missed', 2),
       (
              'task_expired',
              'Task expired',
              'Task expired',
              2
       );
INSERT INTO educational_types (name, description, label)
VALUES ('school', 'School', 'School'),
       ('degree', 'Degree', 'Degree'),
       ('certificate', 'Certificate', 'Certificate'),
       ('other', 'Other', 'Other');
INSERT INTO educational_levels (
              name,
              description,
              label,
              priority,
              educational_type_id
       )
VALUES ('none', 'No formal education', 'None', 10, 1),
       (
              'elementary',
              'Elementary School',
              'Elementary School',
              30,
              1
       ),
       (
              'middle_school',
              'Middle School',
              'Middle School',
              40,
              1
       ),
       (
              'high_school',
              'High School',
              'High School',
              50,
              1
       ),
       (
              'technical',
              'Technical Education',
              'Technical Education',
              60,
              1
       ),
       (
              'higher_education',
              'Higher Education',
              'Higher Education',
              80,
              2
       ),
       (
              'postgraduate',
              'Postgraduate',
              'Postgraduate',
              90,
              2
       ),
       (
              'masters',
              'Master''s Degree',
              'Master''s Degree',
              100,
              2
       ),
       ('doctorate', 'Doctorate', 'Doctorate', 110, 2),
       (
              'post_doctorate',
              'Postdoctoral',
              'Postdoctoral',
              120,
              2
       );
INSERT INTO user_education (
              user_id,
              educational_type_id,
              educational_level_id,
              completion_status_id,
              institution,
              name,
              start_date,
              end_date,
              certification_number,
              certification_url,
              description,
              expiration_date
       )
VALUES (
              v_user_id,
              1,
              4,
              2,
              'Colégio Solução',
              'High School Degree (GED)',
              '2020-01-01',
              '2023-06-01',
              null,
              null,
              null,
              null
       ),
       (
              v_user_id,
              2,
              6,
              4,
              'Anhanguera Educacional',
              'Analysis and System Development',
              '2025-06-01',
              null,
              null,
              null,
              null,
              null
       ),
       (
              v_user_id,
              3,
              null,
              2,
              'SCRUMstudy',
              'Scrum for Operations & DevOps Fundamentals Certified (SODFC™)',
              '2026-08-01',
              null,
              1181284,
              'https://www.scrumstudy.com/certification/verify?type=SODFC&number=',
              'Scrum for Operations & DevOps Fundamentals Certified Certificate from SCRUMstudy',
              '2027-07-01'
       ),
       (
              v_user_id,
              4,
              null,
              2,
              'CILCO (Centro Integrado de Linguas)',
              'C1 - English',
              '2010-02-01',
              '2013-12-10',
              null,
              null,
              'English Language Certificate from CILCO (Centro Integrado de Linguas) - C1',
              null
       ),
       (
              v_user_id,
              3,
              null,
              2,
              'SCRUMstudy',
              'Scrum Fundamentals Certified (SFC™)',
              '2026-08-01',
              null,
              1181260,
              'https://www.scrumstudy.com/certification/verify?type=SFC&number=',
              'Scrum Fundamentals Certified Certificate from SCRUMstudy',
              '2027-08-01'
       );
INSERT INTO marital_statuses (name, description, label)
VALUES ('single', 'Single (never married)', 'Single'),
       ('married', 'Married (married) ', 'Married'),
       ('divorced', 'Divorced (divorced) ', 'Divorced'),
       ('widowed', 'Widowed (spouse died)', 'Widowed'),
       (
              'separated',
              'Separated (separated) ',
              'Separated'
       ),
       (
              'stable_union',
              'Stable Union (live together before marriage)',
              'Stable Union'
       ),
       ('others', 'Others', 'Others');
INSERT INTO disabilities (name, description, label)
VALUES ('visual', 'Visual', 'Visual Impairment'),
       ('hearing', 'Hearing', 'Hearing Impairment'),
       ('mental', 'Mental', 'Mental disorder'),
       ('physical', 'Physical', 'Physical disability'),
       (
              'intellectual',
              'Intellectual',
              'Intellectual disability'
       ),
       ('others', 'Others', 'Others');
INSERT INTO genders (name, description, icon, label)
VALUES ('male', 'Male', 'bi bi-gender-male', 'Male'),
       (
              'female',
              'Female',
              'bi bi-gender-female',
              'Female'
       ),
       (
              'transgender',
              'Transgender',
              'bi bi-gender-transgender',
              'Transgender'
       ),
       (
              'ambiguous',
              'Ambiguous',
              'bi bi-gender-ambiguous',
              'Ambiguous'
       ),
       ('others', 'Others', 'bi bi-question', 'Others');
INSERT INTO locales (name, description, label, i18n_path, flag_path)
VALUES ('en', 'English', 'English', 'en', 'flags/en.png'),
       (
              'es',
              'Spanish',
              'Español',
              'es',
              'flags/es.png'
       ),
       (
              'pt',
              'Portuguese',
              'Português',
              'pt',
              'flags/pt.png'
       );
INSERT INTO contact_categories (name, description, label)
VALUES ('work', 'Work', 'Work'),
       ('personal', 'Personal', 'Personal'),
       ('emergency', 'Emergency', 'Emergency'),
       ('reference', 'Reference', 'Reference'),
       ('others', 'Others', 'Others');
INSERT INTO contact_types (name, description, label)
VALUES ('email', 'Email', 'Email'),
       ('phone', 'Phone', 'Phone'),
       ('mobile', 'Mobile', 'Mobile'),
       ('whatsapp', 'WhatsApp', 'WhatsApp'),
       ('telegram', 'Telegram', 'Telegram'),
       ('skype', 'Skype', 'Skype'),
       ('teamspeak', 'Teamspeak', 'Teamspeak'),
       ('slack', 'Slack', 'Slack'),
       ('teams', 'Microsoft Teams', 'Microsoft Teams'),
       ('others', 'Others', 'Others');
INSERT INTO sexual_orientations (name, description, label)
VALUES (
              'straight',
              'Straight (heterosexual)',
              'Straight'
       ),
       ('gay', 'Gay (homosexual)', 'Gay'),
       ('bisexual', 'Bisexual', 'Bisexual'),
       ('transsexual', 'Transsexual', 'Transsexual'),
       ('asexual', 'Asexual', 'Asexual'),
       ('pansexual', 'Pansexual', 'Pansexual'),
       ('queer', 'Queer', 'Queer'),
       ('lesbian', 'Lesbian', 'Lesbian'),
       ('others', 'Others', 'Others');
INSERT INTO ethnicity (name, label, description)
VALUES (
              'asian',
              'Asian',
              'People of East Asian, Southeast Asian, or related Asian ancestry.'
       ),
       (
              'black_african_descent',
              'Black or African descent',
              'People of African or predominantly African ancestry.'
       ),
       (
              'latin_american',
              'Latin American',
              'People with ancestry originating from Latin America.'
       ),
       (
              'black_latin_american',
              'Black or Latin American',
              'People of African or Latin American ancestry or of mixed African and Latin American ancestry.'
       ),
       (
              'white_european_descent',
              'White or European descent',
              'People of European or predominantly European ancestry.'
       ),
       (
              'indigenous',
              'Indigenous peoples',
              'Peoples indigenous to a particular region, including their descendants.'
       ),
       (
              'middle_eastern_north_african',
              'Middle Eastern or North African',
              'People with ancestry originating primarily from the Middle East or North Africa.'
       ),
       (
              'south_asian',
              'South Asian',
              'People with ancestry originating primarily from South Asia.'
       ),
       (
              'southeast_asian',
              'Southeast Asian',
              'People with ancestry originating primarily from Southeast Asia.'
       ),
       (
              'east_asian',
              'East Asian',
              'People with ancestry originating primarily from East Asia.'
       ),
       (
              'pacific_islander',
              'Pacific Islander',
              'People with ancestry originating from the Pacific Islands.'
       ),
       (
              'latino_hispanic',
              'Latino or Hispanic',
              'People with cultural or ancestral origins associated with Latin America or Hispanic communities.'
       ),
       (
              'central_asian',
              'Central Asian',
              'People with ancestry originating primarily from Central Asia.'
       ),
       (
              'caribbean',
              'Caribbean',
              'People with ancestry originating primarily from Caribbean populations.'
       ),
       (
              'mixed_multiracial',
              'Mixed or Multiracial',
              'People reporting ancestry from multiple demographic or ethnic backgrounds.'
       ),
       (
              'other',
              'Other',
              'Another demographic background not represented by the available categories.'
       ),
       (
              'prefer_not_to_say',
              'Prefer not to say',
              'The individual chooses not to disclose their demographic background.'
       );
INSERT INTO nationality (name, label, description)
VALUES (
              'afghanistan',
              'Afghan',
              'Nationality associated with Afghanistan.'
       ),
       (
              'aland_islands',
              'Åland Island',
              'Nationality associated with Åland Islands.'
       ),
       (
              'albania',
              'Albanian',
              'Nationality associated with Albania.'
       ),
       (
              'algeria',
              'Algerian',
              'Nationality associated with Algeria.'
       ),
       (
              'american_samoa',
              'American Samoan',
              'Nationality associated with American Samoa.'
       ),
       (
              'andorra',
              'Andorran',
              'Nationality associated with Andorra.'
       ),
       (
              'angola',
              'Angolan',
              'Nationality associated with Angola.'
       ),
       (
              'anguilla',
              'Anguillan',
              'Nationality associated with Anguilla.'
       ),
       (
              'antarctica',
              'Antarctic',
              'Nationality associated with Antarctica.'
       ),
       (
              'antigua_and_barbuda',
              'Antiguan or Barbudan',
              'Nationality associated with Antigua and Barbuda.'
       ),
       (
              'argentina',
              'Argentine',
              'Nationality associated with Argentina.'
       ),
       (
              'armenia',
              'Armenian',
              'Nationality associated with Armenia.'
       ),
       (
              'aruba',
              'Aruban',
              'Nationality associated with Aruba.'
       ),
       (
              'australia',
              'Australian',
              'Nationality associated with Australia.'
       ),
       (
              'austria',
              'Austrian',
              'Nationality associated with Austria.'
       ),
       (
              'azerbaijan',
              'Azerbaijani, Azeri',
              'Nationality associated with Azerbaijan.'
       ),
       (
              'bahamas',
              'Bahamian',
              'Nationality associated with Bahamas.'
       ),
       (
              'bahrain',
              'Bahraini',
              'Nationality associated with Bahrain.'
       ),
       (
              'bangladesh',
              'Bangladeshi',
              'Nationality associated with Bangladesh.'
       ),
       (
              'barbados',
              'Barbadian',
              'Nationality associated with Barbados.'
       ),
       (
              'belarus',
              'Belarusian',
              'Nationality associated with Belarus.'
       ),
       (
              'belgium',
              'Belgian',
              'Nationality associated with Belgium.'
       ),
       (
              'belize',
              'Belizean',
              'Nationality associated with Belize.'
       ),
       (
              'benin',
              'Beninese, Beninois',
              'Nationality associated with Benin.'
       ),
       (
              'bermuda',
              'Bermudian, Bermudan',
              'Nationality associated with Bermuda.'
       ),
       (
              'bhutan',
              'Bhutanese',
              'Nationality associated with Bhutan.'
       ),
       (
              'bolivia',
              'Bolivian',
              'Nationality associated with Bolivia.'
       ),
       (
              'bonaire_sint_eustatius_and_saba',
              'Bonaire',
              'Nationality associated with Bonaire, Sint Eustatius and Saba.'
       ),
       (
              'bosnia_and_herzegovina',
              'Bosnian or Herzegovinian',
              'Nationality associated with Bosnia and Herzegovina.'
       ),
       (
              'botswana',
              'Motswana, Botswanan',
              'Nationality associated with Botswana.'
       ),
       (
              'bouvet_island',
              'Bouvet Island',
              'Nationality associated with Bouvet Island.'
       ),
       (
              'brazil',
              'Brazilian',
              'Nationality associated with Brazil.'
       ),
       (
              'british_indian_ocean_territory',
              'BIOT',
              'Nationality associated with British Indian Ocean Territory.'
       ),
       (
              'brunei_darussalam',
              'Bruneian',
              'Nationality associated with Brunei Darussalam.'
       ),
       (
              'bulgaria',
              'Bulgarian',
              'Nationality associated with Bulgaria.'
       ),
       (
              'burkina_faso',
              'Burkinabé',
              'Nationality associated with Burkina Faso.'
       ),
       (
              'burundi',
              'Burundian',
              'Nationality associated with Burundi.'
       ),
       (
              'cabo_verde',
              'Cabo Verdean',
              'Nationality associated with Cabo Verde.'
       ),
       (
              'cambodia',
              'Cambodian',
              'Nationality associated with Cambodia.'
       ),
       (
              'cameroon',
              'Cameroonian',
              'Nationality associated with Cameroon.'
       ),
       (
              'canada',
              'Canadian',
              'Nationality associated with Canada.'
       ),
       (
              'cayman_islands',
              'Caymanian',
              'Nationality associated with Cayman Islands.'
       ),
       (
              'central_african_republic',
              'Central African',
              'Nationality associated with Central African Republic.'
       ),
       (
              'chad',
              'Chadian',
              'Nationality associated with Chad.'
       ),
       (
              'chile',
              'Chilean',
              'Nationality associated with Chile.'
       ),
       (
              'china',
              'Chinese',
              'Nationality associated with China.'
       ),
       (
              'christmas_island',
              'Christmas Island',
              'Nationality associated with Christmas Island.'
       ),
       (
              'cocos_keeling_islands',
              'Cocos Island',
              'Nationality associated with Cocos (Keeling) Islands.'
       ),
       (
              'colombia',
              'Colombian',
              'Nationality associated with Colombia.'
       ),
       (
              'comoros',
              'Comoran, Comorian',
              'Nationality associated with Comoros.'
       ),
       (
              'congo_republic_of_the',
              'Congolese',
              'Nationality associated with Congo (Republic of the).'
       ),
       (
              'congo_democratic_republic_of_the',
              'Congolese',
              'Nationality associated with Congo (Democratic Republic of the).'
       ),
       (
              'cook_islands',
              'Cook Island',
              'Nationality associated with Cook Islands.'
       ),
       (
              'costa_rica',
              'Costa Rican',
              'Nationality associated with Costa Rica.'
       ),
       (
              'cote_d_ivoire',
              'Ivorian',
              'Nationality associated with Côte d''Ivoire.'
       ),
       (
              'croatia',
              'Croatian',
              'Nationality associated with Croatia.'
       ),
       (
              'cuba',
              'Cuban',
              'Nationality associated with Cuba.'
       ),
       (
              'curacao',
              'Curaçaoan',
              'Nationality associated with Curaçao.'
       ),
       (
              'cyprus',
              'Cypriot',
              'Nationality associated with Cyprus.'
       ),
       (
              'czech_republic',
              'Czech',
              'Nationality associated with Czech Republic.'
       ),
       (
              'denmark',
              'Danish',
              'Nationality associated with Denmark.'
       ),
       (
              'djibouti',
              'Djiboutian',
              'Nationality associated with Djibouti.'
       ),
       (
              'dominica',
              'Dominican',
              'Nationality associated with Dominica.'
       ),
       (
              'dominican_republic',
              'Dominican',
              'Nationality associated with Dominican Republic.'
       ),
       (
              'ecuador',
              'Ecuadorian',
              'Nationality associated with Ecuador.'
       ),
       (
              'egypt',
              'Egyptian',
              'Nationality associated with Egypt.'
       ),
       (
              'el_salvador',
              'Salvadoran',
              'Nationality associated with El Salvador.'
       ),
       (
              'equatorial_guinea',
              'Equatorial Guinean, Equatoguinean',
              'Nationality associated with Equatorial Guinea.'
       ),
       (
              'eritrea',
              'Eritrean',
              'Nationality associated with Eritrea.'
       ),
       (
              'estonia',
              'Estonian',
              'Nationality associated with Estonia.'
       ),
       (
              'ethiopia',
              'Ethiopian',
              'Nationality associated with Ethiopia.'
       ),
       (
              'falkland_islands_malvinas',
              'Falkland Island',
              'Nationality associated with Falkland Islands (Malvinas).'
       ),
       (
              'faroe_islands',
              'Faroese',
              'Nationality associated with Faroe Islands.'
       ),
       (
              'fiji',
              'Fijian',
              'Nationality associated with Fiji.'
       ),
       (
              'finland',
              'Finnish',
              'Nationality associated with Finland.'
       ),
       (
              'france',
              'French',
              'Nationality associated with France.'
       ),
       (
              'french_guiana',
              'French Guianese',
              'Nationality associated with French Guiana.'
       ),
       (
              'french_polynesia',
              'French Polynesian',
              'Nationality associated with French Polynesia.'
       ),
       (
              'french_southern_territories',
              'French Southern Territories',
              'Nationality associated with French Southern Territories.'
       ),
       (
              'gabon',
              'Gabonese',
              'Nationality associated with Gabon.'
       ),
       (
              'gambia',
              'Gambian',
              'Nationality associated with Gambia.'
       ),
       (
              'georgia',
              'Georgian',
              'Nationality associated with Georgia.'
       ),
       (
              'germany',
              'German',
              'Nationality associated with Germany.'
       ),
       (
              'ghana',
              'Ghanaian',
              'Nationality associated with Ghana.'
       ),
       (
              'gibraltar',
              'Gibraltar',
              'Nationality associated with Gibraltar.'
       ),
       (
              'greece',
              'Greek, Hellenic',
              'Nationality associated with Greece.'
       ),
       (
              'greenland',
              'Greenlandic',
              'Nationality associated with Greenland.'
       ),
       (
              'grenada',
              'Grenadian',
              'Nationality associated with Grenada.'
       ),
       (
              'guadeloupe',
              'Guadeloupe',
              'Nationality associated with Guadeloupe.'
       ),
       (
              'guam',
              'Guamanian, Guambat',
              'Nationality associated with Guam.'
       ),
       (
              'guatemala',
              'Guatemalan',
              'Nationality associated with Guatemala.'
       ),
       (
              'guernsey',
              'Channel Island',
              'Nationality associated with Guernsey.'
       ),
       (
              'guinea',
              'Guinean',
              'Nationality associated with Guinea.'
       ),
       (
              'guinea_bissau',
              'Bissau-Guinean',
              'Nationality associated with Guinea-Bissau.'
       ),
       (
              'guyana',
              'Guyanese',
              'Nationality associated with Guyana.'
       ),
       (
              'haiti',
              'Haitian',
              'Nationality associated with Haiti.'
       ),
       (
              'heard_island_and_mcdonald_islands',
              'Heard Island or McDonald Islands',
              'Nationality associated with Heard Island and McDonald Islands.'
       ),
       (
              'vatican_city_state',
              'Vatican',
              'Nationality associated with Vatican City State.'
       ),
       (
              'honduras',
              'Honduran',
              'Nationality associated with Honduras.'
       ),
       (
              'hong_kong',
              'Hong Kong, Hong Kongese',
              'Nationality associated with Hong Kong.'
       ),
       (
              'hungary',
              'Hungarian, Magyar',
              'Nationality associated with Hungary.'
       ),
       (
              'iceland',
              'Icelandic',
              'Nationality associated with Iceland.'
       ),
       (
              'india',
              'Indian',
              'Nationality associated with India.'
       ),
       (
              'indonesia',
              'Indonesian',
              'Nationality associated with Indonesia.'
       ),
       (
              'iran',
              'Iranian, Persian',
              'Nationality associated with Iran.'
       ),
       (
              'iraq',
              'Iraqi',
              'Nationality associated with Iraq.'
       ),
       (
              'ireland',
              'Irish',
              'Nationality associated with Ireland.'
       ),
       (
              'isle_of_man',
              'Manx',
              'Nationality associated with Isle of Man.'
       ),
       (
              'israel',
              'Israeli',
              'Nationality associated with Israel.'
       ),
       (
              'italy',
              'Italian',
              'Nationality associated with Italy.'
       ),
       (
              'jamaica',
              'Jamaican',
              'Nationality associated with Jamaica.'
       ),
       (
              'japan',
              'Japanese',
              'Nationality associated with Japan.'
       ),
       (
              'jersey',
              'Channel Island',
              'Nationality associated with Jersey.'
       ),
       (
              'jordan',
              'Jordanian',
              'Nationality associated with Jordan.'
       ),
       (
              'kazakhstan',
              'Kazakhstani, Kazakh',
              'Nationality associated with Kazakhstan.'
       ),
       (
              'kenya',
              'Kenyan',
              'Nationality associated with Kenya.'
       ),
       (
              'kiribati',
              'I-Kiribati',
              'Nationality associated with Kiribati.'
       ),
       (
              'korea_democratic_people_s_republic_of',
              'North Korean',
              'Nationality associated with Korea (Democratic People''s Republic of).'
       ),
       (
              'korea_republic_of',
              'South Korean',
              'Nationality associated with Korea (Republic of).'
       ),
       (
              'kuwait',
              'Kuwaiti',
              'Nationality associated with Kuwait.'
       ),
       (
              'kyrgyzstan',
              'Kyrgyzstani, Kyrgyz, Kirgiz, Kirghiz',
              'Nationality associated with Kyrgyzstan.'
       ),
       (
              'lao_people_s_democratic_republic',
              'Lao, Laotian',
              'Nationality associated with Lao People''s Democratic Republic.'
       ),
       (
              'latvia',
              'Latvian',
              'Nationality associated with Latvia.'
       ),
       (
              'lebanon',
              'Lebanese',
              'Nationality associated with Lebanon.'
       ),
       (
              'lesotho',
              'Basotho',
              'Nationality associated with Lesotho.'
       ),
       (
              'liberia',
              'Liberian',
              'Nationality associated with Liberia.'
       ),
       (
              'libya',
              'Libyan',
              'Nationality associated with Libya.'
       ),
       (
              'liechtenstein',
              'Liechtenstein',
              'Nationality associated with Liechtenstein.'
       ),
       (
              'lithuania',
              'Lithuanian',
              'Nationality associated with Lithuania.'
       ),
       (
              'luxembourg',
              'Luxembourg, Luxembourgish',
              'Nationality associated with Luxembourg.'
       ),
       (
              'macao',
              'Macanese, Chinese',
              'Nationality associated with Macao.'
       ),
       (
              'macedonia_the_former_yugoslav_republic_of',
              'Macedonian',
              'Nationality associated with Macedonia (the former Yugoslav Republic of).'
       ),
       (
              'madagascar',
              'Malagasy',
              'Nationality associated with Madagascar.'
       ),
       (
              'malawi',
              'Malawian',
              'Nationality associated with Malawi.'
       ),
       (
              'malaysia',
              'Malaysian',
              'Nationality associated with Malaysia.'
       ),
       (
              'maldives',
              'Maldivian',
              'Nationality associated with Maldives.'
       ),
       (
              'mali',
              'Malian, Malinese',
              'Nationality associated with Mali.'
       ),
       (
              'malta',
              'Maltese',
              'Nationality associated with Malta.'
       ),
       (
              'marshall_islands',
              'Marshallese',
              'Nationality associated with Marshall Islands.'
       ),
       (
              'martinique',
              'Martiniquais, Martinican',
              'Nationality associated with Martinique.'
       ),
       (
              'mauritania',
              'Mauritanian',
              'Nationality associated with Mauritania.'
       ),
       (
              'mauritius',
              'Mauritian',
              'Nationality associated with Mauritius.'
       ),
       (
              'mayotte',
              'Mahoran',
              'Nationality associated with Mayotte.'
       ),
       (
              'mexico',
              'Mexican',
              'Nationality associated with Mexico.'
       ),
       (
              'micronesia_federated_states_of',
              'Micronesian',
              'Nationality associated with Micronesia (Federated States of).'
       ),
       (
              'moldova_republic_of',
              'Moldovan',
              'Nationality associated with Moldova (Republic of).'
       ),
       (
              'monaco',
              'Monégasque, Monacan',
              'Nationality associated with Monaco.'
       ),
       (
              'mongolia',
              'Mongolian',
              'Nationality associated with Mongolia.'
       ),
       (
              'montenegro',
              'Montenegrin',
              'Nationality associated with Montenegro.'
       ),
       (
              'montserrat',
              'Montserratian',
              'Nationality associated with Montserrat.'
       ),
       (
              'morocco',
              'Moroccan',
              'Nationality associated with Morocco.'
       ),
       (
              'mozambique',
              'Mozambican',
              'Nationality associated with Mozambique.'
       ),
       (
              'myanmar',
              'Burmese',
              'Nationality associated with Myanmar.'
       ),
       (
              'namibia',
              'Namibian',
              'Nationality associated with Namibia.'
       ),
       (
              'nauru',
              'Nauruan',
              'Nationality associated with Nauru.'
       ),
       (
              'nepal',
              'Nepali, Nepalese',
              'Nationality associated with Nepal.'
       ),
       (
              'netherlands',
              'Dutch, Netherlandic',
              'Nationality associated with Netherlands.'
       ),
       (
              'new_caledonia',
              'New Caledonian',
              'Nationality associated with New Caledonia.'
       ),
       (
              'new_zealand',
              'New Zealand, NZ',
              'Nationality associated with New Zealand.'
       ),
       (
              'nicaragua',
              'Nicaraguan',
              'Nationality associated with Nicaragua.'
       ),
       (
              'niger',
              'Nigerien',
              'Nationality associated with Niger.'
       ),
       (
              'nigeria',
              'Nigerian',
              'Nationality associated with Nigeria.'
       ),
       (
              'niue',
              'Niuean',
              'Nationality associated with Niue.'
       ),
       (
              'norfolk_island',
              'Norfolk Island',
              'Nationality associated with Norfolk Island.'
       ),
       (
              'northern_mariana_islands',
              'Northern Marianan',
              'Nationality associated with Northern Mariana Islands.'
       ),
       (
              'norway',
              'Norwegian',
              'Nationality associated with Norway.'
       ),
       (
              'oman',
              'Omani',
              'Nationality associated with Oman.'
       ),
       (
              'pakistan',
              'Pakistani',
              'Nationality associated with Pakistan.'
       ),
       (
              'palau',
              'Palauan',
              'Nationality associated with Palau.'
       ),
       (
              'palestine_state_of',
              'Palestinian',
              'Nationality associated with Palestine, State of.'
       ),
       (
              'panama',
              'Panamanian',
              'Nationality associated with Panama.'
       ),
       (
              'papua_new_guinea',
              'Papua New Guinean, Papuan',
              'Nationality associated with Papua New Guinea.'
       ),
       (
              'paraguay',
              'Paraguayan',
              'Nationality associated with Paraguay.'
       ),
       (
              'peru',
              'Peruvian',
              'Nationality associated with Peru.'
       ),
       (
              'philippines',
              'Philippine, Filipino',
              'Nationality associated with Philippines.'
       ),
       (
              'pitcairn',
              'Pitcairn Island',
              'Nationality associated with Pitcairn.'
       ),
       (
              'poland',
              'Polish',
              'Nationality associated with Poland.'
       ),
       (
              'portugal',
              'Portuguese',
              'Nationality associated with Portugal.'
       ),
       (
              'puerto_rico',
              'Puerto Rican',
              'Nationality associated with Puerto Rico.'
       ),
       (
              'qatar',
              'Qatari',
              'Nationality associated with Qatar.'
       ),
       (
              'reunion',
              'Réunionese, Réunionnais',
              'Nationality associated with Réunion.'
       ),
       (
              'romania',
              'Romanian',
              'Nationality associated with Romania.'
       ),
       (
              'russian_federation',
              'Russian',
              'Nationality associated with Russian Federation.'
       ),
       (
              'rwanda',
              'Rwandan',
              'Nationality associated with Rwanda.'
       ),
       (
              'saint_barthelemy',
              'Barthélemois',
              'Nationality associated with Saint Barthélemy.'
       ),
       (
              'saint_helena_ascension_and_tristan_da_cunha',
              'Saint Helenian',
              'Nationality associated with Saint Helena, Ascension and Tristan da Cunha.'
       ),
       (
              'saint_kitts_and_nevis',
              'Kittitian or Nevisian',
              'Nationality associated with Saint Kitts and Nevis.'
       ),
       (
              'saint_lucia',
              'Saint Lucian',
              'Nationality associated with Saint Lucia.'
       ),
       (
              'saint_martin_french_part',
              'Saint-Martinoise',
              'Nationality associated with Saint Martin (French part).'
       ),
       (
              'saint_pierre_and_miquelon',
              'Saint-Pierrais or Miquelonnais',
              'Nationality associated with Saint Pierre and Miquelon.'
       ),
       (
              'saint_vincent_and_the_grenadines',
              'Saint Vincentian, Vincentian',
              'Nationality associated with Saint Vincent and the Grenadines.'
       ),
       (
              'samoa',
              'Samoan',
              'Nationality associated with Samoa.'
       ),
       (
              'san_marino',
              'Sammarinese',
              'Nationality associated with San Marino.'
       ),
       (
              'sao_tome_and_principe',
              'São Toméan',
              'Nationality associated with Sao Tome and Principe.'
       ),
       (
              'saudi_arabia',
              'Saudi, Saudi Arabian',
              'Nationality associated with Saudi Arabia.'
       ),
       (
              'senegal',
              'Senegalese',
              'Nationality associated with Senegal.'
       ),
       (
              'serbia',
              'Serbian',
              'Nationality associated with Serbia.'
       ),
       (
              'seychelles',
              'Seychellois',
              'Nationality associated with Seychelles.'
       ),
       (
              'sierra_leone',
              'Sierra Leonean',
              'Nationality associated with Sierra Leone.'
       ),
       (
              'singapore',
              'Singaporean',
              'Nationality associated with Singapore.'
       ),
       (
              'sint_maarten_dutch_part',
              'Sint Maarten',
              'Nationality associated with Sint Maarten (Dutch part).'
       ),
       (
              'slovakia',
              'Slovak',
              'Nationality associated with Slovakia.'
       ),
       (
              'slovenia',
              'Slovenian, Slovene',
              'Nationality associated with Slovenia.'
       ),
       (
              'solomon_islands',
              'Solomon Island',
              'Nationality associated with Solomon Islands.'
       ),
       (
              'somalia',
              'Somali, Somalian',
              'Nationality associated with Somalia.'
       ),
       (
              'south_africa',
              'South African',
              'Nationality associated with South Africa.'
       ),
       (
              'south_georgia_and_the_south_sandwich_islands',
              'South Georgia or South Sandwich Islands',
              'Nationality associated with South Georgia and the South Sandwich Islands.'
       ),
       (
              'south_sudan',
              'South Sudanese',
              'Nationality associated with South Sudan.'
       ),
       (
              'spain',
              'Spanish',
              'Nationality associated with Spain.'
       ),
       (
              'sri_lanka',
              'Sri Lankan',
              'Nationality associated with Sri Lanka.'
       ),
       (
              'sudan',
              'Sudanese',
              'Nationality associated with Sudan.'
       ),
       (
              'suriname',
              'Surinamese',
              'Nationality associated with Suriname.'
       ),
       (
              'svalbard_and_jan_mayen',
              'Svalbard',
              'Nationality associated with Svalbard and Jan Mayen.'
       ),
       (
              'swaziland',
              'Swazi',
              'Nationality associated with Swaziland.'
       ),
       (
              'sweden',
              'Swedish',
              'Nationality associated with Sweden.'
       ),
       (
              'switzerland',
              'Swiss',
              'Nationality associated with Switzerland.'
       ),
       (
              'syrian_arab_republic',
              'Syrian',
              'Nationality associated with Syrian Arab Republic.'
       ),
       (
              'taiwan_province_of_china',
              'Chinese, Taiwanese',
              'Nationality associated with Taiwan, Province of China.'
       ),
       (
              'tajikistan',
              'Tajikistani',
              'Nationality associated with Tajikistan.'
       ),
       (
              'tanzania_united_republic_of',
              'Tanzanian',
              'Nationality associated with Tanzania, United Republic of.'
       ),
       (
              'thailand',
              'Thai',
              'Nationality associated with Thailand.'
       ),
       (
              'timor_leste',
              'Timorese',
              'Nationality associated with Timor-Leste.'
       ),
       (
              'togo',
              'Togolese',
              'Nationality associated with Togo.'
       ),
       (
              'tokelau',
              'Tokelauan',
              'Nationality associated with Tokelau.'
       ),
       (
              'tonga',
              'Tongan',
              'Nationality associated with Tonga.'
       ),
       (
              'trinidad_and_tobago',
              'Trinidadian or Tobagonian',
              'Nationality associated with Trinidad and Tobago.'
       ),
       (
              'tunisia',
              'Tunisian',
              'Nationality associated with Tunisia.'
       ),
       (
              'turkey',
              'Turkish',
              'Nationality associated with Turkey.'
       ),
       (
              'turkmenistan',
              'Turkmen',
              'Nationality associated with Turkmenistan.'
       ),
       (
              'turks_and_caicos_islands',
              'Turks and Caicos Island',
              'Nationality associated with Turks and Caicos Islands.'
       ),
       (
              'tuvalu',
              'Tuvaluan',
              'Nationality associated with Tuvalu.'
       ),
       (
              'uganda',
              'Ugandan',
              'Nationality associated with Uganda.'
       ),
       (
              'ukraine',
              'Ukrainian',
              'Nationality associated with Ukraine.'
       ),
       (
              'united_arab_emirates',
              'Emirati, Emirian, Emiri',
              'Nationality associated with United Arab Emirates.'
       ),
       (
              'united_kingdom_of_great_britain_and_northern_ireland',
              'British, UK',
              'Nationality associated with United Kingdom of Great Britain and Northern Ireland.'
       ),
       (
              'united_states_minor_outlying_islands',
              'American',
              'Nationality associated with United States Minor Outlying Islands.'
       ),
       (
              'united_states_of_america',
              'American',
              'Nationality associated with United States of America.'
       ),
       (
              'uruguay',
              'Uruguayan',
              'Nationality associated with Uruguay.'
       ),
       (
              'uzbekistan',
              'Uzbekistani, Uzbek',
              'Nationality associated with Uzbekistan.'
       ),
       (
              'vanuatu',
              'Ni-Vanuatu, Vanuatuan',
              'Nationality associated with Vanuatu.'
       ),
       (
              'venezuela_bolivarian_republic_of',
              'Venezuelan',
              'Nationality associated with Venezuela (Bolivarian Republic of).'
       ),
       (
              'vietnam',
              'Vietnamese',
              'Nationality associated with Vietnam.'
       ),
       (
              'virgin_islands_british',
              'British Virgin Island',
              'Nationality associated with Virgin Islands (British).'
       ),
       (
              'virgin_islands_u_s',
              'U.S. Virgin Island',
              'Nationality associated with Virgin Islands (U.S.).'
       ),
       (
              'wallis_and_futuna',
              'Wallis and Futuna, Wallisian or Futunan',
              'Nationality associated with Wallis and Futuna.'
       ),
       (
              'western_sahara',
              'Sahrawi, Sahrawian, Sahraouian',
              'Nationality associated with Western Sahara.'
       ),
       (
              'yemen',
              'Yemeni',
              'Nationality associated with Yemen.'
       ),
       (
              'zambia',
              'Zambian',
              'Nationality associated with Zambia.'
       ),
       (
              'zimbabwe',
              'Zimbabwean',
              'Nationality associated with Zimbabwe.'
       ) ON CONFLICT DO NOTHING;
INSERT INTO tenants (
              domain,
              slug,
              active,
              created_by,
              subscription_type_id,
              subscription_status_id
       )
VALUES (
              'https://purosabor.vcnexus.com',
              'puro-sabor',
              1,
              1,
              6,
              1
       ) ON CONFLICT DO NOTHING
RETURNING id INTO v_tenant_id;
INSERT INTO business (
              tenant_id,
              trade_name,
              type,
              tax_id,
              fantasy_name,
              active
       )
VALUES (
              v_tenant_id,
              'Puro Sabor (MEI)',
              1,
              '62.728.369/0001-72',
              'Puro Sabor',
              1
       ) ON CONFLICT DO NOTHING
RETURNING id INTO v_business_id;
INSERT INTO business_brandings (
              business_id,
              app_name,
              primary_color,
              accent_color,
              text_color,
              background_color,
              font_style,
              button_style
       )
VALUES (
              v_business_id,
              'Puro Sabor',
              '#2c6693',
              '#7fb44d80',
              '#000000',
              '#FFFFFF',
              'normal',
              'normal'
       ) ON CONFLICT DO NOTHING;
INSERT INTO tenants (
              domain,
              slug,
              active,
              created_by,
              subscription_type_id,
              subscription_status_id
       )
VALUES (
              'https://vjsolucoes.vcnexus.com',
              'vjsolucoes',
              1,
              1,
              7,
              1
       ) ON CONFLICT DO NOTHING
RETURNING id INTO v_tenant_id;
INSERT INTO business (
              tenant_id,
              trade_name,
              type,
              tax_id,
              fantasy_name,
              active
       )
VALUES (
              v_tenant_id,
              'VJ Soluções (MEI)',
              1,
              '17.017.023/0001-72',
              'VJ Soluções',
              1
       ) ON CONFLICT DO NOTHING
RETURNING id INTO v_business_id;
INSERT INTO business_brandings (
              business_id,
              app_name,
              primary_color,
              accent_color,
              text_color,
              background_color,
              font_style,
              button_style
       )
VALUES (
              v_business_id,
              'VJ Soluções',
              '#2c6693',
              '#7fb44d80',
              '#000000',
              '#FFFFFF',
              'normal',
              'normal'
       ) ON CONFLICT DO NOTHING;
RETURN QUERY
SELECT v_tenant_id,
       v_business_id,
       v_user_id;
END;
$$;
SELECT *
FROM initialize_system ();