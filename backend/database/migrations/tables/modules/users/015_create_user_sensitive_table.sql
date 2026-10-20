CREATE TABLE user_sensitive (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    socialname varchar(100),
    gender_id smallint,
    religion_id smallint,
    ethnicity_id smallint,
    nationality_id smallint,
    marital_status_id smallint,
    sexual_orientation_id smallint,
    disability_id smallint,
    updated_at timestamp
);
COMMENT ON COLUMN "user_sensitive"."gender_id" IS 'see: gender table';
COMMENT ON COLUMN "user_sensitive"."religion_id" IS 'see: religions table';
COMMENT ON COLUMN "user_sensitive"."ethnicity_id" IS 'see: ethnicity table';
COMMENT ON COLUMN "user_sensitive"."nationality_id" IS 'see: nationality table';
COMMENT ON COLUMN "user_sensitive"."marital_status_id" IS '1 = Single, 2 = Married, 3 = Divorced, 4 = Widowed, 5 = Stable Union, 6 = Others, null = Unknown, see: marital_status table';
COMMENT ON COLUMN "user_sensitive"."sexual_orientation_id" IS 'see: sexual_orientation table';
COMMENT ON COLUMN "user_sensitive"."disability_id" IS 'see: disabilities table';
COMMENT ON COLUMN "user_sensitive"."uuid" IS 'V7 UUID';