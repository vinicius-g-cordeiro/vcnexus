CREATE TABLE user_address (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    purpose varchar NOT NULL,
    address varchar(255) NOT NULL,
    zip_code varchar(15),
    city_id bigint,
    state_id bigint,
    country_id bigint,
    neighborhood varchar(255),
    complement varchar(255),
    reference varchar(255),
    extra_info varchar(500)
);
COMMENT ON COLUMN "user_address"."purpose" IS 'eg: Home, Work, etc..';
COMMENT ON COLUMN "user_address"."uuid" IS 'V7 UUID';