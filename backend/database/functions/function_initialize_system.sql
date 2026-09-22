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
        ('View users', 'View users', 1, 1, 'users.view',1),
        ('Create users', 'Create users', 1, 1, 'users.create',1),
        ('Update users', 'Update users', 1, 1, 'users.update',1),
        ('Delete users', 'Delete users', 1, 1, 'users.delete',1),
        ('View roles', 'View roles', 1, 1, 'roles.view',1),
        ('Create roles', 'Create roles', 1, 1, 'roles.create',1),
        ('Update roles', 'Update roles', 1, 1, 'roles.update',1),
        ('Delete roles', 'Delete roles', 1, 1, 'roles.delete',1),
        ('View tenants', 'View tenants', 1, 1, 'tenants.view',1),
        ('Create tenants', 'Create tenants', 1, 1, 'tenants.create',1),
        ('Update tenants', 'Update tenants', 1, 1, 'tenants.update',1),
        ('Delete tenants', 'Delete tenants', 1, 1, 'tenants.delete',1),
        ('View permissions', 'View permissions', 1, 1, 'permissions.view',1),
        ('Create permissions', 'Create permissions', 1, 1, 'permissions.create',1),
        ('Update permissions', 'Update permissions', 1, 1, 'permissions.update',1),
        ('Delete permissions', 'Delete permissions', 1, 1, 'permissions.delete',1),
        ('View suppliers', 'View suppliers', 1, 1, 'suppliers.view',1),
        ('Create suppliers', 'Create suppliers', 1, 1, 'suppliers.create',1),
        ('Update suppliers', 'Update suppliers', 1, 1, 'suppliers.update',1),
        ('Delete suppliers', 'Delete suppliers', 1, 1, 'suppliers.delete',1),
        ('View products', 'View products', 1, 1, 'products.view',1),
        ('Create products', 'Create products', 1, 1, 'products.create',1),
        ('Update products', 'Update products', 1, 1, 'products.update',1),
        ('Delete products', 'Delete products', 1, 1, 'products.delete',1),
        ('View services', 'View services', 1, 1, 'services.view',1),
        ('Create services', 'Create services', 1, 1, 'services.create',1),
        ('Update services', 'Update services', 1, 1, 'services.update',1),
        ('Delete services', 'Delete services', 1, 1, 'services.delete',1),
        ('View clients', 'View clients', 1, 1, 'clients.view',1),
        ('Create clients', 'Create clients', 1, 1, 'clients.create',1),
        ('Update clients', 'Update clients', 1, 1, 'clients.update',1),
        ('Delete clients', 'Delete clients', 1, 1, 'clients.delete',1),
        ('View contracts', 'View contracts', 1, 1, 'contracts.view',1),
        ('Create contracts', 'Create contracts', 1, 1, 'contracts.create',1),
        ('Update contracts', 'Update contracts', 1, 1, 'contracts.update',1),
        ('Delete contracts', 'Delete contracts', 1, 1, 'contracts.delete',1),
        ('View deliveries', 'View deliveries', 1, 1, 'deliveries.view',1),
        ('Create deliveries', 'Create deliveries', 1, 1, 'deliveries.create',1),
        ('Update deliveries', 'Update deliveries', 1, 1, 'deliveries.update',1),
        ('Delete deliveries', 'Delete deliveries', 1, 1, 'deliveries.delete',1),
        ('View orders', 'View orders', 1, 1, 'orders.view',1),
        ('Create orders', 'Create orders', 1, 1, 'orders.create',1),
        ('Update orders', 'Update orders', 1, 1, 'orders.update',1),
        ('Delete orders', 'Delete orders', 1, 1, 'orders.delete',1),
        ('View payments', 'View payments', 1, 1, 'payments.view',1),
        ('Create payments', 'Create payments', 1, 1, 'payments.create',1),
        ('Update payments', 'Update payments', 1, 1, 'payments.update',1),
        ('Delete payments', 'Delete payments', 1, 1, 'payments.delete',1),
        ('View receipts', 'View receipts', 1, 1, 'receipts.view',1),
        ('Create receipts', 'Create receipts', 1, 1, 'receipts.create',1),
        ('Update receipts', 'Update receipts', 1, 1, 'receipts.update',1),
        ('Delete receipts', 'Delete receipts', 1, 1, 'receipts.delete',1),
        ('View workers', 'View workers', 1, 1, 'workers.view',1),
        ('Create workers', 'Create workers', 1, 1, 'workers.create',1),
        ('Update workers', 'Update workers', 1, 1, 'workers.update',1),
        ('Delete workers', 'Delete workers', 1, 1, 'workers.delete',1),
        ('View documents', 'View documents', 1, 1, 'documents.view',1),
        ('Create documents', 'Create documents', 1, 1, 'documents.create',1),
        ('Update documents', 'Update documents', 1, 1, 'documents.update',1),
        ('Delete documents', 'Delete documents', 1, 1, 'documents.delete',1),
        ('View salaries', 'View salaries', 1, 1, 'salaries.view',1),
        ('Create salaries', 'Create salaries', 1, 1, 'salaries.create',1),
        ('Update salaries', 'Update salaries', 1, 1, 'salaries.update',1),
        ('Delete salaries', 'Delete salaries', 1, 1, 'salaries.delete',1)
    ON CONFLICT DO NOTHING;

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
        marital_status_id,
        birthdate,
        nationality_id,
        locale
    )
    VALUES (
        v_user_id,
        'Vinicius',
        'Gonçalves',
        'Cordeiro',
        1,
        '1999-04-23',
        1,
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