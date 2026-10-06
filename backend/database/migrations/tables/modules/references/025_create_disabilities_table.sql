CREATE TABLE disabilities (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    label varchar(100) NOT NULL,
    description varchar(100)
);
COMMENT ON COLUMN "disabilities"."uuid" IS 'V7 UUID';