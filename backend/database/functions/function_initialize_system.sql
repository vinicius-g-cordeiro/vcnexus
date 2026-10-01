CREATE OR REPLACE FUNCTION initialize_system()
RETURNS TABLE (
    tenant_id BIGINT,
    business_id BIGINT,
    user_id BIGINT
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_tenant_id BIGINT;
    v_business_id BIGINT;
    v_user_id BIGINT;
BEGIN

    CREATE EXTENSION IF NOT EXISTS pgcrypto;
    CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
    CREATE EXTENSION IF NOT EXISTS unaccent;

    /*
     * 1. System roles
     */
    INSERT INTO roles (
        active,
        name,
        description,
        created_by,
        tenant_id
    )
    VALUES
        (1, 'Super Administrator', 'System administrator with full access', 1, null),
        (1, 'Administrator', 'System administrator with limited access', 1, null),
        (1, 'User', 'System user with limited access', 1, null),
        (1, 'Guest', 'System guest with limited access', 1, null),
        (1, 'Public', 'System public with very limited access', 1, null),
        (1, 'Human Resources', 'Human Resources with access to: workers, documents, contracts, salaries, benefits', 1, 1),
        (1, 'Manager', 'Manager with access to: workers, clients, products, services, deliveries', 1, 1),
        (1, 'Employee', 'Employee with access to: clients,products, services, deliveries', 1, 1),
        (1, 'Operator', 'Operator with access to: products, deliveries', 1, 1),
        (1, 'Logistics', 'Logistics with access to: deliveries', 1, 1)
    ON CONFLICT DO NOTHING;

    
    /*
     * 2. System permissions
     */
    INSERT INTO permissions (
        name,
        description,
        active,
        created_by,
        slug,
		tenant_id
    )
    VALUES
        ('List', 'View menus', 1, 1, 'menus.view',1),
        ('Create', 'Create menus', 1, 1, 'menus.create',1),
        ('Update', 'Update menus', 1, 1, 'menus.update',1),
        ('Delete', 'Delete menus', 1, 1, 'menus.delete',1),
        ('List', 'View users', 1, 1, 'users.view',1),
        ('Create', 'Create users', 1, 1, 'users.create',1),
        ('Update', 'Update users', 1, 1, 'users.update',1),
        ('Delete', 'Delete users', 1, 1, 'users.delete',1),
        ('List', 'View roles', 1, 1, 'roles.view',1),
        ('Create', 'Create roles', 1, 1, 'roles.create',1),
        ('Update', 'Update roles', 1, 1, 'roles.update',1),
        ('Delete', 'Delete roles', 1, 1, 'roles.delete',1),
        ('List', 'View tenants', 1, 1, 'tenants.view',1),
        ('Create', 'Create tenants', 1, 1, 'tenants.create',1),
        ('Update', 'Update tenants', 1, 1, 'tenants.update',1),
        ('Delete', 'Delete tenants', 1, 1, 'tenants.delete',1),
        ('List', 'View business', 1, 1, 'business.view',1),
        ('Create', 'Create business', 1, 1, 'business.create',1),
        ('Update', 'Update business', 1, 1, 'business.update',1),
        ('Delete', 'Delete business', 1, 1, 'business.delete',1),
        ('List', 'View permissions', 1, 1, 'permissions.view',1),
        ('Create', 'Create permissions', 1, 1, 'permissions.create',1),
        ('Update', 'Update permissions', 1, 1, 'permissions.update',1),
        ('Delete', 'Delete permissions', 1, 1, 'permissions.delete',1),
        ('List', 'View suppliers', 1, 1, 'suppliers.view',1),
        ('Create', 'Create suppliers', 1, 1, 'suppliers.create',1),
        ('Update', 'Update suppliers', 1, 1, 'suppliers.update',1),
        ('Delete', 'Delete suppliers', 1, 1, 'suppliers.delete',1),
        ('List', 'View products', 1, 1, 'products.view',1),
        ('Create', 'Create products', 1, 1, 'products.create',1),
        ('Update', 'Update products', 1, 1, 'products.update',1),
        ('Delete', 'Delete products', 1, 1, 'products.delete',1),
        ('List', 'View services', 1, 1, 'services.view',1),
        ('Create', 'Create services', 1, 1, 'services.create',1),
        ('Update', 'Update services', 1, 1, 'services.update',1),
        ('Delete', 'Delete services', 1, 1, 'services.delete',1),
        ('List', 'View clients', 1, 1, 'clients.view',1),
        ('Create', 'Create clients', 1, 1, 'clients.create',1),
        ('Update', 'Update clients', 1, 1, 'clients.update',1),
        ('Delete', 'Delete clients', 1, 1, 'clients.delete',1),
        ('List', 'View contracts', 1, 1, 'contracts.view',1),
        ('Create', 'Create contracts', 1, 1, 'contracts.create',1),
        ('Update', 'Update contracts', 1, 1, 'contracts.update',1),
        ('Delete', 'Delete contracts', 1, 1, 'contracts.delete',1),
        ('List', 'View deliveries', 1, 1, 'deliveries.view',1),
        ('Create', 'Create deliveries', 1, 1, 'deliveries.create',1),
        ('Update', 'Update deliveries', 1, 1, 'deliveries.update',1),
        ('Delete', 'Delete deliveries', 1, 1, 'deliveries.delete',1),
        ('List', 'View orders', 1, 1, 'orders.view',1),
        ('Create', 'Create orders', 1, 1, 'orders.create',1),
        ('Update', 'Update orders', 1, 1, 'orders.update',1),
        ('Delete', 'Delete orders', 1, 1, 'orders.delete',1),
        ('List', 'View payments', 1, 1, 'payments.view',1),
        ('Create', 'Create payments', 1, 1, 'payments.create',1),
        ('Update', 'Update payments', 1, 1, 'payments.update',1),
        ('Delete', 'Delete payments', 1, 1, 'payments.delete',1),
        ('List', 'View receipts', 1, 1, 'receipts.view',1),
        ('Create', 'Create receipts', 1, 1, 'receipts.create',1),
        ('Update', 'Update receipts', 1, 1, 'receipts.update',1),
        ('Delete', 'Delete receipts', 1, 1, 'receipts.delete',1),
        ('List', 'View workers', 1, 1, 'workers.view',1),
        ('Create', 'Create workers', 1, 1, 'workers.create',1),
        ('Update', 'Update workers', 1, 1, 'workers.update',1),
        ('Delete', 'Delete workers', 1, 1, 'workers.delete',1),
        ('List', 'View documents', 1, 1, 'documents.view',1),
        ('Create', 'Create documents', 1, 1, 'documents.create',1),
        ('Update', 'Update documents', 1, 1, 'documents.update',1),
        ('Delete', 'Delete documents', 1, 1, 'documents.delete',1),
        ('List', 'View salaries', 1, 1, 'salaries.view',1),
        ('Create', 'Create salaries', 1, 1, 'salaries.create',1),
        ('Update', 'Update salaries', 1, 1, 'salaries.update',1),
        ('Delete', 'Delete salaries', 1, 1, 'salaries.delete',1),
        ('Authentication', 'Authentication Me', 1, 1, 'authentication.me',1)
    ON CONFLICT DO NOTHING;


    INSERT INTO menus ("uuid", active, parent_id, "label", icon, route, "order", permissions, tenant_id) 
			VALUES
            (uuidv7(), 1, null, 'Dashboard', 'bi-speedometer2', 'dashboard', 0, ARRAY['dashboard.view'], null),
            (uuidv7(), 1, null, 'Home', 'bi-house-fill', 'home', 0, null, null),
            (uuidv7(), 1, null, 'Users', 'bi-person-fill', 'users', 0, ARRAY['users.view', 'users.create', 'users.reports', 'users.documents'], null),
            (uuidv7(), 1, null, 'Menus', 'bi-list', 'menus', 0, ARRAY['menus.view', 'menus.create', 'menus.update', 'menus.delete'], null),
            (uuidv7(), 1, null, 'Roles', 'bi-people-fill', 'roles', 0, ARRAY['roles.view', 'roles.create'], null),
            (uuidv7(), 1, null, 'Tenants', 'bi-building', 'tenants', 0, ARRAY['tenants.view', 'tenants.create'], null),
            (uuidv7(), 1, null, 'Permissions', 'bi-shield-lock-fill', 'permissions', 0, ARRAY['permissions.view', 'permissions.create'], null),
            (uuidv7(), 1, null, 'Business', 'bi-building', 'business', 0, ARRAY['business.view', 'business.create'], null),
            (uuidv7(), 1, null, 'Suppliers', 'bi-truck', 'suppliers', 0, ARRAY['suppliers.view', 'suppliers.create'], null),
            (uuidv7(), 1, null, 'Products', 'bi-box-seam', 'products', 0, ARRAY['products.view', 'products.create'], null),
            (uuidv7(), 1, null, 'Deliveries', 'bi-truck', 'deliveries', 0, ARRAY['deliveries.view', 'deliveries.create'], null),
            (uuidv7(), 1, null, 'Orders', 'bi-basket-fill', 'orders', 0, ARRAY['orders.view', 'orders.create'], null),
            (uuidv7(), 1, null, 'Payments', 'bi-cash-stack', 'payments', 0, ARRAY['payments.view', 'payments.create'], null),
            (uuidv7(), 1, null, 'Receipts', 'bi-receipt-cutoff', 'receipts', 0, ARRAY['receipts.view', 'receipts.create'], null),
            (uuidv7(), 1, null, 'Workers', 'bi-people-fill', 'workers', 0, ARRAY['workers.view', 'workers.create'], null),
            (uuidv7(), 1, null, 'Documents', 'bi-file-earmark-text-fill', 'documents', 0, ARRAY['documents.view', 'documents.create'], null),
            (uuidv7(), 1, null, 'Salaries', 'bi-cash-coin', 'salaries', 0, ARRAY['salaries.view', 'salaries.create'], null),
            (uuidv7(), 1, null, 'Authentication', 'bi-shield-lock-fill', 'authentication', 0, ARRAY['authentication.me'], null)
    ON CONFLICT DO NOTHING;

    INSERT INTO menus ("uuid", active, parent_id, "label", icon, route, "order", permissions, tenant_id)
            VALUES  (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'users'), 'List', 'bi-person-fill', 'users.index', 1, ARRAY['users.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'users'), 'Create', 'bi-plus', 'users.create', 2, ARRAY['users.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'users'), 'Reports', 'bi-file-earmark-text-fill', 'users.reports', 3, ARRAY['users.reports'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'users'), 'Documents', 'bi-file-earmark-text-fill', 'users.documents', 4, ARRAY['users.documents'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'menus'), 'List', 'bi-list', 'menus.index', 1, ARRAY['menus.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'menus'), 'Create', 'bi-plus', 'menus.create', 2, ARRAY['menus.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'menus'), 'Delete', 'bi-trash', 'menus.delete', 4, ARRAY['menus.delete'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'roles'), 'List', 'bi-list', 'roles.index', 1, ARRAY['roles.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'roles'), 'Create', 'bi-plus', 'roles.create', 2, ARRAY['roles.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'tenants'), 'List', 'bi-list', 'tenants.index', 1, ARRAY['tenants.view'], null),    
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'tenants'), 'Create', 'bi-plus', 'tenants.create', 2, ARRAY['tenants.create'], null),    
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'permissions'), 'List', 'bi-list', 'permissions.index', 1, ARRAY['permissions.view'], null),    
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'permissions'), 'Create', 'bi-plus', 'permissions.create', 2, ARRAY['permissions.create'], null),    
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'business'), 'List', 'bi-list', 'business.index', 1, ARRAY['business.view'], null),    
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'business'), 'Create', 'bi-plus', 'business.create', 2, ARRAY['business.create'], null),    
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'suppliers'), 'List', 'bi-list', 'suppliers.index', 1, ARRAY['suppliers.view'], null),    
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'suppliers'), 'Create', 'bi-plus', 'suppliers.create', 2, ARRAY['suppliers.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'products'), 'List', 'bi-list', 'products.index', 1, ARRAY['products.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'products'), 'Create', 'bi-plus', 'products.create', 2, ARRAY['products.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'deliveries'), 'List', 'bi-list', 'deliveries.index', 1, ARRAY['deliveries.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'deliveries'), 'Create', 'bi-plus', 'deliveries.create', 2, ARRAY['deliveries.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'orders'), 'List', 'bi-list', 'orders.index', 1, ARRAY['orders.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'orders'), 'Create', 'bi-plus', 'orders.create', 2, ARRAY['orders.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'payments'), 'List', 'bi-list', 'payments.index', 1, ARRAY['payments.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'payments'), 'Create', 'bi-plus', 'payments.create', 2, ARRAY['payments.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'receipts'), 'List', 'bi-list', 'receipts.index', 1, ARRAY['receipts.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'receipts'), 'Create', 'bi-plus', 'receipts.create', 2, ARRAY['receipts.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'workers'), 'List', 'bi-list', 'workers.index', 1, ARRAY['workers.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'workers'), 'Create', 'bi-plus', 'workers.create', 2, ARRAY['workers.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'documents'), 'List', 'bi-file-earmark-text-fill', 'documents.index', 1, ARRAY['documents.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'documents'), 'Create', 'bi-plus', 'documents.create', 2, ARRAY['documents.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'reports'), 'List', 'bi-file-earmark-text-fill', 'reports.index', 1, ARRAY['reports.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'reports'), 'Create', 'bi-plus', 'reports.create', 2, ARRAY['reports.create'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'salaries'), 'List', 'bi-cash-coin', 'salaries.index', 1, ARRAY['salaries.view'], null),
                    (uuidv7(), 1, (SELECT id FROM menus WHERE route = 'salaries'), 'Create', 'bi-plus', 'salaries.create', 2, ARRAY['salaries.create'], null);

                    

    /*
     * 3. Tenant
     */
    INSERT INTO tenants (
        domain,
        slug,
        active,
        created_by,
        subscription_type,
        subscription_status
    )
    VALUES (
        'localhost',
        'vcnexus',
        1,
        1,
        1,
        1
    )
    ON CONFLICT DO NOTHING
    RETURNING id INTO v_tenant_id;


    /*
     * 4. Business
     */
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


    /*
     * 5. Business branding
     */
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


    /*
     * 6. Super administrator
     */
    INSERT INTO user_credentials (
        email,
        password,
        active,
        created_by
    )
    VALUES (
        'vcnexus.vinicius@gmail.com',
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

    INSERT INTO user_consents (
        user_id,
        purpose,
        legal_basis,
        granted_at
    )
    VALUES (
        v_user_id,
        'diversity reporting',
        'legal obligation',
        NOW()
    );

    INSERT INTO user_sensitive (
        user_id,
        socialname,
        gender_id,
        religion_id,
        ethnicity_id,
        sexual_orientation,
        disability_id,
        marital_status_id,
        nationality_id,
        updated_at
    )
    VALUES (
        v_user_id,
        'VCNexus Admin',
        null,
        1,
        null,
        null,
        null,
        1,
        1,
        NOW()
    );

    INSERT INTO user_address (
        user_id,
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
        v_user_id,
        'Home',
        'Fictional Address, Building 1, Apartment 303',
        '72800-000',
        1,
        1,
        1,
        'Fictional Neighborhood',
        null,
        null,
        null
    )
    ON CONFLICT DO NOTHING;


    INSERT INTO user_contact (
        user_id,
        type,
        value,
        label,
        primary_contact,
        category,
        person
    )
    VALUES ( v_user_id, 2, '+55 61 9 9179-5618', 'WhatsApp', 1, 'Personal', null ),
        ( v_user_id, 2, '+55 61 9 9399-7699', 'WhatsApp', 0, 'Reference', 'Jociely(Wife)' ),
        ( v_user_id, 1, 'vinicordeirogo@gmail.com', 'Email', 1, 'Personal', null ),
        ( v_user_id, 1, 'vcnexus.vinicius@gmail.com', 'Email', 0, 'Work', null )
    ON CONFLICT DO NOTHING;



    /*
     * 7. Tenant membership
     */
    INSERT INTO tenant_memberships (
        tenant_id,
        user_id,
        role_id,
		created_by,
        active
    )
    SELECT
        v_tenant_id,
        v_user_id,
        id,
		v_user_id,
        1
    FROM roles
    WHERE name = 'Super Administrator'
    ON CONFLICT DO NOTHING;


    INSERT INTO user_permissions (user_id, permission_id, created_by)
    SELECT
        v_user_id,
        p.id,
		1
    FROM permissions p
    ON CONFLICT DO NOTHING;


    INSERT INTO user_roles (user_id, role_id, created_by) 
    VALUES (
        v_user_id
        , 1
        , 1
    )
    ON CONFLICT DO NOTHING;


    INSERT INTO role_permissions (role_id, permission_id)
    SELECT
        r.id,
        p.id
    FROM roles r
    CROSS JOIN permissions p
    WHERE r.id = 1
    ON CONFLICT DO NOTHING;



    /*
     * 8. Return initialization context
     */
    RETURN QUERY
    SELECT
        v_tenant_id,
        v_business_id,
        v_user_id;
END;
$$;



/*
 * 9. Call function
 */
SELECT * FROM initialize_system();