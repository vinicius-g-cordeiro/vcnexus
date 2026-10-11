CREATE TABLE ownable_types (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL
);
COMMENT ON COLUMN "ownable_types"."name" IS 'Table name of the ownable object, eg: users, tenants, projects, etc.';
COMMENT ON COLUMN "ownable_types"."uuid" IS 'V7 UUID';