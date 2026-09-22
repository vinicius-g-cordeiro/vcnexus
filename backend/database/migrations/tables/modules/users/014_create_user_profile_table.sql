CREATE TABLE user_profile (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    firstname varchar(100) NOT NULL,
    surname varchar(100),
    lastname varchar(100) NOT NULL,
    marital_status_id smallint,
    birthdate timestamp,
    nationality_id smallint,
    locale varchar(5) DEFAULT 'en-US'
);
COMMENT ON COLUMN "user_profile"."marital_status_id" IS '1 = Single, 2 = Married, 3 = Divorced, 4 = Widowed, 5 = Stable Union, 6 = Others, null = Unknown, see: marital_status table';
COMMENT ON COLUMN "user_profile"."nationality_id" IS 'see: nationality table';
COMMENT ON COLUMN "user_profile"."uuid" IS 'V7 UUID';