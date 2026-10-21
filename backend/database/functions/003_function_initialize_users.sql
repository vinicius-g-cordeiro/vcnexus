CREATE OR REPLACE FUNCTION initialize_users() RETURNS TABLE (user_id BIGINT ) LANGUAGE plpgsql AS $$
DECLARE
v_user_id BIGINT;
BEGIN CREATE EXTENSION IF NOT EXISTS pgcrypto;
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS unaccent;

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
              locale,
              avatar
       )
VALUES (
              v_user_id,
              'Vinicius',
              'Gonçalves',
              'Cordeiro',
              '1999-04-23',
              'pt-BR',
              'upload-01a15bcc-5320-7ae1-897f-3afb11f72395-6ad80327ae152-20102026211119.jpg'
       );
INSERT INTO user_consents (user_id, purpose, legal_basis_id, granted_at)
VALUES (
              v_user_id,
              'Diversity reporting',
              4,
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
SELECT 1,
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
              '2026-08-01',
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
              '2026-08-01',
              1181260,
              'https://www.scrumstudy.com/certification/verify?type=SFC&number=',
              'Scrum Fundamentals Certified Certificate from SCRUMstudy',
              '2027-08-01'
       );


RETURN QUERY
SELECT v_user_id;
END;
$$;
SELECT *
FROM initialize_users ();

