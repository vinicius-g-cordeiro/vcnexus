CREATE TABLE user_consents (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    user_id bigint NOT NULL,
    purpose varchar(64) NOT NULL,
    legal_basis varchar(32) NOT NULL,
    granted_at timestamp NOT NULL DEFAULT current_timestamp,
    revoked_at timestamp
);
COMMENT ON COLUMN "user_consents"."purpose" IS 'eg: ''diversity reporting'', ''legal compliance''';
COMMENT ON COLUMN "user_consents"."legal_basis" IS 'eg: ''legal obligation'' , ''consent''';
COMMENT ON COLUMN "user_consents"."uuid" IS 'V7 UUID';