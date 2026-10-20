CREATE TABLE marital_statuses (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    label varchar(100) NOT NULL,
    description varchar(100),
    icon varchar(100)
);
COMMENT ON COLUMN "marital_statuses"."label" IS 'eg: Single, Married, etc...';
COMMENT ON COLUMN "marital_statuses"."icon" IS 'Icon name for the marital status, fa-solid fa-rings-wedding for example... ';
COMMENT ON COLUMN "marital_statuses"."uuid" IS 'V7 UUID';