CREATE TABLE user_contact (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    type smallint NOT NULL,
    value varchar(100) NOT NULL,
    label varchar(100) NOT NULL,
    primary_contact smallint NOT NULL,
    category_id bigint,
    person varchar(100)
);
COMMENT ON COLUMN "user_contact"."type" IS 'eg: 1: Email, 2: Phone, 3: Website, ...';
COMMENT ON COLUMN "user_contact"."category_id" IS 'eg: Home, Work, Reference, etc..';
COMMENT ON COLUMN "user_contact"."person" IS 'eg: Wife, Husband, Mother, Father, etc..';
COMMENT ON COLUMN "user_contact"."uuid" IS 'V7 UUID';