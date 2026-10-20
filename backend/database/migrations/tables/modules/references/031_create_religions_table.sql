CREATE TABLE religions (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    label varchar(100) NOT NULL,
    description varchar(100),
    icon varchar(100)
);
COMMENT ON COLUMN "religions"."icon" IS 'Icon for this religion for example fa-solid fa-cross for Christianity with Font Awesome';
COMMENT ON COLUMN "religions"."uuid" IS 'V7 UUID';