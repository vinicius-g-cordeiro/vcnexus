CREATE TABLE locales (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    label varchar(100) NOT NULL,
    description varchar(200),
    i18n_path varchar(255) NOT NULL,
    flag_path varchar(255) NOT NULL
);
COMMENT ON COLUMN "locales"."flag_path" IS 'locale flag image path url';
COMMENT ON COLUMN "locales"."uuid" IS 'V7 UUID';