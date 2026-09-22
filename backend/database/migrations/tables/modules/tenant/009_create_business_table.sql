CREATE TABLE business (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    trade_name varchar(255) NOT NULL,
    fantasy_name varchar(255) NOT NULL,
    tax_id varchar(30) NOT NULL,
    type smallint,
    state_registration varchar(30),
    municipal_registration varchar(30),
    description varchar(500),
    website varchar(100),
    tenant_id bigint NOT NULL DEFAULT 1
);
COMMENT ON COLUMN "business"."type" IS '1: MEI(Micro Empreendedor Individual), 2: SLU(Sociedade Limitada Unipessoal), 3: EI(Empresa Individual), 4: LTDA(Sociedade Limitada), 5: SS(Sociedade Simples), 6: SA(Sociedade Anônima), etc..';
COMMENT ON COLUMN "business"."uuid" IS 'V7 UUID';