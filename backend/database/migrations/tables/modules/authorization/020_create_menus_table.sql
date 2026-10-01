CREATE TABLE menus (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY ,
    uuid uuid NOT NULL DEFAULT uuidv7(),
    active smallint NOT NULL DEFAULT 1,
    parent_id bigint,
    label varchar(100) NOT NULL,
    icon varchar(100),
    route varchar(100),
    "order" bigint NOT NULL,
    permissions varchar(100)[],
    tenant_id bigint
);
COMMENT ON COLUMN "menus"."parent_id" IS 'Parent menu id. eg: if is null is a root menu item, if not then is a child menu item from the parent id menu item. ';
COMMENT ON COLUMN "menus"."label" IS 'Menu label. eg: Users';
COMMENT ON COLUMN "menus"."icon" IS 'Menu icon. eg: bi bi-person-fill for Bootstrap Icons';
COMMENT ON COLUMN "menus"."route" IS 'Route name on Vue.js router. eg: users.index';
COMMENT ON COLUMN "menus"."order" IS 'Menu order. eg: 1 for first position';
COMMENT ON COLUMN "menus"."permissions" IS 'Menu permissions. eg: ["users.index", "users.create"]';
COMMENT ON COLUMN "menus"."uuid" IS 'V7 UUID';