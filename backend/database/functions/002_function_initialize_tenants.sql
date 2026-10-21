CREATE OR REPLACE FUNCTION initialize_tenants() RETURNS TABLE ( tenant_id BIGINT)
LANGUAGE plpgsql AS $$

DECLARE v_tenant_id BIGINT;
v_business_id BIGINT;

BEGIN

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

INSERT INTO business (
              tenant_id,
              trade_name,
              tax_id,
              fantasy_name,
              active,
              legal_entity_type_id,
              type_id
       )
VALUES (
              v_tenant_id,
              'VCNexus (MEI)',
              '000.000.000/0001-01',
              'VCNexus',
              1,
              1,
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
              button_style,
              logo
       )
VALUES (
              v_business_id,
              'VCNexus',
              '#2c6693',
              '#7fb44d80',
              '#000000',
              '#FFFFFF',
              'normal',
              'normal',
              '6ad7c33fec46b2.682380831792525119.png'
       );

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
              tax_id,
              fantasy_name,
              active,
              legal_entity_type_id,
              type_id
       )
VALUES (
              v_tenant_id,
              'Puro Sabor (MEI)',
              '62.728.369/0001-72',
              'Puro Sabor',
              1,
              1,
              7
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
              button_style,
              logo
       )
VALUES (
              v_business_id,
              'Puro Sabor',
              '#2c6693',
              '#7fb44d80',
              '#000000',
              '#FFFFFF',
              'normal',
              'normal',
              'puro_sabor_logo.png'
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
              tax_id,
              fantasy_name,
              active,
              legal_entity_type_id,
              type_id
       )
VALUES (
              v_tenant_id,
              'VJ Soluções (MEI)',
              '17.017.023/0001-72',
              'VJ Soluções',
              1,
              2,
              5
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
              button_style,
              logo
       )
VALUES (
              v_business_id,
              'VJ Soluções',
              '#2c6693',
              '#7fb44d80',
              '#000000',
              '#FFFFFF',
              'normal',
              'normal',
              '827af675-5f5d-45c7-976b-c552937f89ab.jpeg'
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
              'https://cerradogstudio.vcnexus.com',
              'cerradogstudio',
              1,
              1,
              7,
              1
       ) ON CONFLICT DO NOTHING
RETURNING id INTO v_tenant_id;
INSERT INTO business (
              tenant_id,
              trade_name,
              tax_id,
              fantasy_name,
              active,
              legal_entity_type_id,
              type_id
       )
VALUES (
              v_tenant_id,
              'Cerrado G. Studios (SA)',
              '09.014.012/0001-15',
              'Cerrado G. Studios',
              1,
              1,
              5
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
              button_style,
              logo
       )
VALUES (
              v_business_id,
              'Cerrado G. Studios',
              '#2c6693',
              '#7fb44d80',
              '#000000',
              '#FFFFFF',
              'normal',
              'normal',
              'file_6ad7fb1ad3fae3.865741851792539418.png'
       ) ON CONFLICT DO NOTHING;
END;
$$;
SELECT *
FROM initialize_tenants ();
