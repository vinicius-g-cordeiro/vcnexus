CREATE OR REPLACE FUNCTION initialize_references() RETURNS TABLE (v_id BIGINT) LANGUAGE plpgsql AS $$ BEGIN
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
INSERT INTO legal_basis (name, label, description)
VALUES (
        'consent',
        'Consent',
        'Legal consented by the user'
    ),
    (
        'legal_obligation',
        'Legal Obligation',
        'Legal obligation'
    ),
    (
        'legitimate_interest',
        'Legitimate Interest',
        'Legitimate interest'
    ),
    ('contract', 'Contract', 'Contract') ON CONFLICT DO NOTHING;
INSERT INTO subscription_statuses (name, label, description)
VALUES('active', 'Active', 'Active subscription'),
    (
        'suspended',
        'Suspended',
        'Suspended subscription'
    ),
    ('canceled', 'Canceled', 'Canceled subscription'),
    ('expired', 'Expired', 'Expired subscription') ON CONFLICT DO NOTHING;

INSERT INTO business_types (name, label, description)
VALUES ('real_estate', 'Real Estate', 'Real Estate'),
    ('union', 'Union', 'Union'),
    ('education', 'Education', 'Education'),
    (
        'medical_clinic',
        'Medical Clinic',
        'Medical Clinic'
    ),
    (
        'technological',
        'Technological',
        'Technological'
    ),
    ('beauty', 'Healthy & Beauty', 'Healthy & Beauty'),
    (
        'food',
        'Food & Restaurants',
        'Food & Restaurants'
    ),
    ('sports', 'Sports & Fitness', 'Sports & Fitness'),
    ('commerce', 'Commerce', 'Commerce'),
    (
        'tourism',
        'Tourism & Travel',
        'Tourism & Travel'
    ),
    ('others', 'Others', 'Others') ON CONFLICT DO NOTHING;
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
INSERT INTO marital_statuses (name, description, label)
VALUES ('single', 'Single (never married)', 'Single'),
    ('married', 'Married (married) ', 'Married'),
    ('divorced', 'Divorced (divorced) ', 'Divorced'),
    ('widowed', 'Widowed (spouse died)', 'Widowed'),
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
INSERT INTO ethnicities (name, label, description)
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
INSERT INTO legal_entity_types (name, label, description)
VALUES -- BRAZIL
    (
        'BR_MEI',
        'MEI - Micro Empreendedor Individual',
        'Brazil: Micro Empreendedor Individual. Simplified individual structure for small-scale entrepreneurs.'
    ),
    (
        'BR_SLU',
        'SLU - Sociedade Limitada Unipessoal',
        'Brazil: Sociedade Limitada Unipessoal. Single-member limited liability company that protects personal assets without partners.'
    ),
    (
        'BR_EI',
        'EI - Empresa Individual',
        'Brazil: Empresa Individual. Individual business model where the owner’s personal assets are legally tied to the company.'
    ),
    (
        'BR_LTDA',
        'LTDA - Sociedade Limitada',
        'Brazil: Sociedade Limitada. The most common multi-partner corporate structure where liability is restricted to share quotas.'
    ),
    (
        'BR_SS',
        'SS - Sociedade Simples',
        'Brazil: Sociedade Simples. Partnerships tailored for intellectual, scientific, or artistic professions (e.g., doctors, lawyers).'
    ),
    (
        'BR_SA',
        'SA - Sociedade Anônima',
        'Brazil: Sociedade Anônima. Corporations split by shares, used for public or large private organizations raising capital.'
    ),
    -- UNITED STATES
    (
        'US_LLC',
        'LLC - Limited Liability Company',
        'United States: Limited Liability Company. A hybrid structure offering asset protection with pass-through taxation rules.'
    ),
    (
        'US_C_CORP',
        'C-Corp - C-Corporation',
        'United States: C-Corporation. Standard legal corporate structure where owners and shareholders are taxed separately.'
    ),
    (
        'US_S_CORP',
        'S-Corp - S-Corporation',
        'United States: S-Corporation. A special corporate status allowing profits and losses to pass directly to shareholders’ tax returns.'
    ),
    -- UNITED KINGDOM
    (
        'GB_LTD',
        'Ltd - Private Limited Company',
        'United Kingdom: Private Limited Company. Privately held entity where individual shareholder liability is strictly limited to their shares.'
    ),
    (
        'GB_PLC',
        'PLC - Public Limited Company',
        'United Kingdom: Public Limited Company. Publicly traded corporation whose shares are available to the general public.'
    ),
    -- GERMANY
    (
        'DE_GMBH',
        'GmbH - Gesellschaft mit beschränkter Haftung',
        'Germany: Gesellschaft mit beschränkter Haftung. The standard private corporate entity offering limited personal liability.'
    ),
    -- FRANCE
    (
        'FR_SAS',
        'SAS - Société par actions simplifiée',
        'France: Société par ações simplificada. Simplified joint-stock setup highly favored by modern technology startups.'
    ),
    -- ARGENTINA
    (
        'AR_SRL',
        'SRL - Sociedad de Responsabilidad Limitada',
        'Argentina: Sociedad de Responsabilidad Limitada. Limited liability company where capital is split into quotas among partners.'
    ),
    (
        'AR_SA',
        'S.A. - Sociedad Anónima',
        'Argentina: Sociedad Anónima. Traditional corporation structure where capital is represented by tradable shares.'
    ),
    (
        'AR_SAS',
        'SAS - Sociedad por Acciones Simplificadas',
        'Argentina: Sociedad por Acciones Simplificadas. A modern, digitized, and highly simplified corporation format.'
    ),
    -- CANADA
    (
        'CA_SOLE_PROP',
        'Sole Proprietorship',
        'Canada: Unincorporated business structure managed by a single individual with direct personal liability.'
    ),
    (
        'CA_CORP',
        'Corporation',
        'Canada: Incorporated legal entity distinct from its shareholders, providing robust asset protection.'
    ),
    (
        'CA_PARTNERSHIP',
        'Partnership',
        'Canada: Business structure where two or more parties share legal ownership, operational profits, and financial liabilities.'
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
END;
$$;
SELECT *
FROM initialize_references();
