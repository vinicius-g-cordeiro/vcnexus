CREATE TABLE countries (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    description varchar(200),
    geonames_id bigint,
    iso_alpha2 varchar,
    iso_alpha3 varchar
);
COMMENT ON COLUMN "countries"."uuid" IS 'V7 UUID';