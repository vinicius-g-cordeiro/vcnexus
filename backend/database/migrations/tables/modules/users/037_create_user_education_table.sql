CREATE TABLE user_education (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    educational_level_id bigint,
    educational_type_id bigint,
    completion_status_id bigint NOT NULL,
    institution varchar(255) NOT NULL,
    name varchar(255) NOT NULL,
    start_date timestamp NOT NULL,
    end_date timestamp,
    expiration_date timestamp,
    certification_number varchar,
    certification_url varchar(255),
    description text
);
COMMENT ON COLUMN "user_education"."uuid" IS 'V7 UUID';