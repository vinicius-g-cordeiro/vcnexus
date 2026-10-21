CREATE TABLE documents (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name smallint NOT NULL DEFAULT 1,
    label smallint DEFAULT 1,
    tags varchar(255),
    slug varchar(100),
    path varchar(500) NOT NULL,
    type_id bigint NOT NULL,
    owner_type_id bigint NOT NULL,
    owner_id bigint NOT NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp,
    deleted_at timestamp,
    created_by bigint,
    updated_by bigint,
    deleted_by bigint,
    deleted_reason varchar(500)
);
COMMENT ON COLUMN "documents"."uuid" IS 'V7 UUID';