CREATE TABLE states (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    description varchar(200),
    code varchar(100) NOT NULL,
    country_id bigint NOT NULL,
    iso_alpha2 bigint,
    iso_alpha3 bigint,
    geonames_id bigint NOT NULL
);
COMMENT ON COLUMN "states"."uuid" IS 'V7 UUID';