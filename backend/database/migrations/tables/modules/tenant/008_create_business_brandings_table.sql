CREATE TABLE business_brandings (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    business_id bigint NOT NULL DEFAULT 1,
    logo varchar(255) DEFAULT '',
    app_name varchar(255),
    primary_color varchar(100),
    accent_color varchar(100) NOT NULL,
    text_color varchar(100) NOT NULL,
    background_color varchar(100) NOT NULL,
    font_style varchar(100) NOT NULL,
    button_style varchar(100) NOT NULL
);
COMMENT ON COLUMN "business_brandings"."uuid" IS 'V7 UUID';