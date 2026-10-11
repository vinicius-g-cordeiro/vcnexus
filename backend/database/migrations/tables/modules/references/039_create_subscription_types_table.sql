CREATE TABLE subscription_types (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    label varchar(100) NOT NULL,
    description varchar(100) NOT NULL
);
COMMENT ON COLUMN "subscription_types"."uuid" IS 'V7 UUID';