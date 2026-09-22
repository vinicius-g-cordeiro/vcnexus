CREATE TABLE user_sensitive (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    socialname varchar(100),
    gender_id smallint,
    religion_id smallint,
    ethnicity_id smallint,
    sexual_orientation smallint,
    disability_id smallint,
    updated_at timestamp
);
COMMENT ON COLUMN "user_sensitive"."gender_id" IS 'see: gender table';
COMMENT ON COLUMN "user_sensitive"."religion_id" IS 'see: religions table';
COMMENT ON COLUMN "user_sensitive"."ethnicity_id" IS 'see: ethnicity table';
COMMENT ON COLUMN "user_sensitive"."sexual_orientation" IS 'see: sexual_orientation table';
COMMENT ON COLUMN "user_sensitive"."disability_id" IS 'see: disabilities table';
COMMENT ON COLUMN "user_sensitive"."uuid" IS 'V7 UUID';