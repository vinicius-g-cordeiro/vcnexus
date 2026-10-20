CREATE TABLE user_profile (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    firstname varchar(100) NOT NULL,
    surname varchar(100),
    lastname varchar(100) NOT NULL,
    birthdate timestamp,
    locale varchar(5) DEFAULT 'en-US',
    avatar varchar(500)
);
COMMENT ON COLUMN "user_profile"."uuid" IS 'V7 UUID';