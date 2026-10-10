CREATE TABLE completion_statuses (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    name varchar(100) NOT NULL,
    label varchar(100) NOT NULL,
    description varchar(100),
    type smallint
);
COMMENT ON COLUMN "completion_statuses"."type" IS 'Type of completion status, to be used for filtering, if null, it will be used for anything thats needs a completion status, eg: 1: Educational Levels, 2: Tasks, 3: Deliveries, etc...';
COMMENT ON COLUMN "completion_statuses"."uuid" IS 'V7 UUID';