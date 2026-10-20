CREATE TABLE contacts (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    owner_type_id bigint NOT NULL,
    owner_id bigint NOT NULL,
    type_id smallint NOT NULL,
    value varchar(100) NOT NULL,
    label varchar(100) NOT NULL,
    primary_contact smallint NOT NULL,
    category_id bigint,
    person varchar(100)
);
COMMENT ON COLUMN "contacts"."type_id" IS 'eg: 1: Email, 2: Phone, 3: Website, ... see contact_types table';
COMMENT ON COLUMN "contacts"."category_id" IS 'eg: Home, Work, Reference, etc.., see contact_categories table';
COMMENT ON COLUMN "contacts"."person" IS 'eg: person name: Jhon doe (brother), jane doe (sister), etc...';
COMMENT ON COLUMN "contacts"."uuid" IS 'V7 UUID';