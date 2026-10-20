CREATE TABLE cities (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    description varchar(200),
    geonames_id bigint NOT NULL,
    iso_alpha2 bigint,
    iso_alpha3 bigint,
    state_id bigint,
    country_id bigint
);
COMMENT ON COLUMN "cities"."uuid" IS 'V7 UUID';