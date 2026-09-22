CREATE TABLE user_credentials (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    email varchar(60) NOT NULL,
    password varchar(128) NOT NULL,
    blocked smallint,
    remember smallint,
    token varchar(255),
    token_expires_at timestamp,
    token_created_at timestamp,
    last_login_at timestamp,
    last_login_at_local timestamp,
    last_login_ip varchar(100),
    last_login_agent varchar(100),
    created_at timestamp DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp,
    deleted_at timestamp,
    created_by bigint,
    updated_by bigint,
    deleted_by bigint,
    deleted_reason varchar(500)
);
COMMENT ON COLUMN "user_credentials"."remember" IS 'Users Remember for login (1 = Remember), so the user dont need to login again ';
COMMENT ON COLUMN "user_credentials"."uuid" IS 'V7 UUID';