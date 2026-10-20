CREATE TABLE sexual_orientations (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    description varchar(100),
    label varchar(100) NOT NULL,
    icon varchar(100)
);
COMMENT ON COLUMN "sexual_orientations"."icon" IS 'eg: icon from fontawesome or bootstrap icons... ';
COMMENT ON COLUMN "sexual_orientations"."uuid" IS 'V7 UUID';