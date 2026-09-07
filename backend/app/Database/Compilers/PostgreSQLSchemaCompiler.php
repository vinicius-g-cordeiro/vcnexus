<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Database\Compilers;

use App\Shared\Connection;
use App\Database\Attributes\ForeignKeyConstraint;
use App\Database\Attributes\Index;
use App\Database\Attributes\UniqueConstraint;
use App\Database\Schema\Schema;
use App\Exceptions\DatabaseNotCreatedException;
use App\Shared\Response;
use Throwable;
use InvalidArgumentException;


class PostgreSQLSchemaCompiler extends Connection
{

    function __construct($dbConnection = null, public ?Schema $schema = null)
    {
        parent::__construct($dbConnection);

        $this->schema = $schema;
    }

    public function createTable()
    {
        $tableName = $this->schema->table;
        $creationQuery = $this->createTableQuery($tableName);
        $creationQuery .= $this->buildIndexesQuery();
        $creationQuery .= $this->buildCommentsQuery($tableName);

        $response = false;
        $this->getConnection()->StartTrans();
        try {

            $response = $this->getConnection()->Execute($creationQuery);

            if ($this->getConnection()->HasFailedTrans()) {
                throw new \RuntimeException('Transaction failed.');
            }

            $this->getConnection()->CompleteTrans();
        } catch (Throwable $th) {
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();
            throw new DatabaseNotCreatedException($th->getMessage(), $th->getCode(), $th->getPrevious());
        }

    }

    public function createTableQuery(string $tableName = 'example')
    {
        $definitions = [];
        foreach (Schema::columns($this->schema) as $attribute) {
            if (!isset($attribute->column))
                continue;

            $definitions[] = $this->buildColumnDefinition($attribute->column);
        }

        $definitions[] = $this->buildPrimaryKeyDefinition();

        foreach (Schema::constraints($this->schema) as $constraint) {
            $definitions[] = $this->buildConstraintsDefinition($constraint);
        }

        return sprintf('CREATE TABLE IF NOT EXISTS "%s" (' . "\r\n" . '%s' . "\r\n" . ");\r\n", $tableName, implode(",\r\n", $definitions));
    }

    private function buildColumnDefinition(?object $column): string
    {
        $definition = sprintf('"%s" %s', $column->name, $column->type);

        if ($column->nullable === false) {
            $definition .= ' NOT NULL ';
        }

        if (isset($column->default) && $column->default !== '') {
            $definition .= $this->buildDefaultDefinition($column->default);
        }

        return $definition;
    }

    private function buildDefaultDefinition(mixed $default): string
    {
        if (!is_string($default)) {
            return ' DEFAULT ' . $default;
        }

        if (preg_match('/\bDEFAULT\b/i', $default)) {
            return $default;
        }

        return ' DEFAULT ' . $default;
    }

    private function buildPrimaryKeyDefinition(): string
    {
        $idColumn = Schema::get('id', $this->schema);

        return sprintf('PRIMARY KEY (%s)', $idColumn->name);
    }
    private function buildConstraintsDefinition(object $constraint): string
    {
        return match (true) {
            $constraint instanceof UniqueConstraint => $this->buildUniqueConstraint($constraint),
            $constraint instanceof ForeignKeyConstraint => $this->buildForeignKeyConstraint($constraint),
            default => throw new InvalidArgumentException('Unsupported constraint type.'),
        };
    }


    private function buildUniqueConstraint(UniqueConstraint $constraint): string
    {
        return sprintf('CONSTRAINT "%s" UNIQUE (%s)', $constraint->name, $this->quoteColumns($constraint->columns));
    }

    private function buildForeignKeyConstraint(ForeignKeyConstraint $constraint): string
    {
        $sql = sprintf('CONSTRAINT "%s"  FOREIGN KEY (%s) REFERENCES "%s" (%s)', $constraint->name, $this->quoteColumns($constraint->foreignKeys), $constraint->references, $this->quoteColumns($constraint->columns));

        if (isset($constraint->actionOnUpdate) && $constraint->actionOnUpdate === false) {
            $sql .= ' ON UPDATE NO ACTION';
        }

        if (isset($constraint->actionOnDelete) && $constraint->actionOnDelete === true) {
            $sql .= ' ON DELETE ' . $constraint->deleteAction;
        }

        if ($constraint->deferred === true) {
            $sql .= ' DEFERRABLE INITIALLY DEFERRED ';
        }

        return $sql;
    }

    private function buildIndexesQuery(): string
    {
        $queries = [];

        foreach (Schema::indexes($this->schema) as $index) {
            $queries[] = $this->buildIndexQuery($index);
        }

        return implode("\r\n", $queries);
    }

    private function buildIndexQuery(Index $index): string
    {
        $sql = 'CREATE ';
        if ($index->unique === true) {
            $sql .= 'UNIQUE ';
        }

        $sql .= sprintf('INDEX IF NOT EXISTS "%s" ON "%s" (%s)', $index->name, $index->references, $this->quoteColumns($index->columns));

        if (isset($index->condition) && !empty($index->condition)) {
            $sql .= ' WHERE ' . $this->buildIndexCondition($index->condition);
        }

        return $sql . ";\r\n";
    }

    private function buildIndexCondition(array $conditions)
    {
        $conditionParts = [];
        foreach ($conditions as $column => $value) {
            $conditionParts[] = sprintf('"%s" = %s', $column, $value);
        }

        return implode(' AND ', $conditionParts);
    }


    private function buildCommentsQuery(string $tableName): string
    {
        $comments = [];

        foreach (Schema::columns($this->schema) as $attribute) {
            if (!isset($attribute->column))
                continue;

            $column = $attribute->column;

            if (!isset($column->comment) || $column->comment === '')
                continue;

            $comments[] = sprintf("COMMENT ON COLUMN \"%s\".\"%s\" IS '%s';", $tableName, $column->name, $column->comment);
        }

        return implode("\r\n", $comments) . "\r\n";
    }


    private function quoteColumns(array $columns): string
    {
        return implode(',', array_map(static function (string $column): string {
            return '"' . $column . '"';
        }, $columns));
    }

    public function initDefaultsUsers()
    {
        $sqlAdminPassword = password_hash(trim(file_get_contents(trim(getenv('ADMIN_PASSWORD')))), PASSWORD_BCRYPT, ['cost' => 12]);
        $sql = "
INSERT INTO public.tenants (\"name\", modules, active, slug) VALUES('VCNexus', ARRAY['1'::character varying(4)], 1, 'vcnexus');
";
        $this->getConnection()->StartTrans();
        try {
            $result = $this->getConnection()->Execute($sql);

            if ($this->getConnection()->HasFailedTrans()) {
                throw new \RuntimeException('Failed to add initial tenants.');
            }

            $this->getConnection()->CompleteTrans();

        } catch (Throwable $e) {
            Response::log(data: $e);
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();

            throw $e;
        }

        $sql = "
insert
    into
    public.users
(\"uuid\",
    created_at,
    created_at_local,
    updated_at,
    updated_at_local,
    deleted_at,
    deleted_at_local,
    created_by,
    updated_by,
    deleted_by,
    tenant_id,
    active,
    \"name\",
    \"password\",
    surname,
    lastname,
    nickname,
    birthdate,
    email,
    phone,
    gender,
    marital_status,
    sexual_orientation,
    religion,
    \"blocked\",
    blocked_by,
    blocked_at,
    blood_type,
    blood_factor,
    locale,
    last_login,
    last_login_local,
    last_ip,
    last_agent,
    role,
    roles,
    permissions)
values(uuidv4(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,null, null, null, null, null, null, 1, 1, 'SuperAdministrator', '" . $sqlAdminPassword . "', 'Admin', '', array['administrator'::character varying(100)], '1999-04-23', 'vinismtpgo@gmail.com', '', 0, 0, 0, 0, null, null, null, null, null, '', null, null, null, '', 1, array['1'::character varying(100)], array['users.view'::character varying(100)]);
";
        $this->getConnection()->StartTrans();
        try {
            $result = $this->getConnection()->Execute($sql);

            if ($this->getConnection()->HasFailedTrans()) {
                throw new \RuntimeException('Transaction failed.');
            }

            $this->getConnection()->CompleteTrans();

        } catch (Throwable $e) {
            Response::log(data: $e);
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();

            throw $e;
        }
        $sql = "

insert into public.usernames (\"username\", active, user_id, created_by) VALUES('administrator.vcnexus', 1, 1, 1);";

        $this->getConnection()->StartTrans();

        try {
            $result = $this->getConnection()->Execute($sql);

            if ($this->getConnection()->HasFailedTrans()) {
                throw new \RuntimeException('Failed to add default username.');
            }

            $this->getConnection()->CompleteTrans();

        } catch (Throwable $e) {
            Response::log(data: $e);
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();

            throw $e;
        }


        $sql = "insert
    into
    public.business
(\"uuid\",
    created_at,
    created_at_local,
    updated_at,
    updated_at_local,
    deleted_at,
    deleted_at_local,
    created_by,
    updated_by,
    deleted_by,
    tenant_id,
    active,
    legal_name,
    trade_name,
    description,
    \"type\",
    tax_id,
    municipal_registration,
    state_registration,
    email,
    website,
    phone,
    categories)
values(uuidv4(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null, null, null, null, 1, 0, 0, 1, 1, 'VCNexus (MEI)', 'VCNexus', '', 1, '62.728.369/0001-72', '', '', 'cerradogstudio.viniciuscordeiro@gmail.com', 'https://www.vcnexus.com.br',array['+55 61 9 9179-5618'::character varying(20)],'{10}');
";
        $this->getConnection()->StartTrans();

        try {
            $result = $this->getConnection()->Execute($sql);

            if ($this->getConnection()->HasFailedTrans()) {
                throw new \RuntimeException('Failed to add business.');
            }

            $this->getConnection()->CompleteTrans();

        } catch (Throwable $e) {
            Response::log(data: $e);
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();

            throw $e;
        }


        $sql = "insert
        into
        public.business_branding
    (\"uuid\",
        created_at,
        created_at_local,
        updated_at,
        updated_at_local,
        deleted_at,
        deleted_at_local,
        created_by,
        updated_by,
        deleted_by,
        tenant_id,
        active,
        business_id,
        logo,
        app_name,
        \"primaryColor\",
        \"accentColor\",
        \"textColor\",
        \"backgroundColor\",
        \"fontStyle\",
        \"buttonStyle\")
    values(uuidv4(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null, null, null, null, 1, 0, 0, 1, 1, 1, '', 'VCNexus', 'emerald-500', 'gold-500', 'zinc-900', 'zinc-100', 'inter', 'rounded-sm');
    ";


        $this->getConnection()->StartTrans();

        try {
            $result = $this->getConnection()->Execute($sql);

            if ($this->getConnection()->HasFailedTrans()) {
                throw new \RuntimeException('Failed to add business branding.');
            }

            $this->getConnection()->CompleteTrans();

        } catch (Throwable $e) {
            Response::log(data: $e);
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();

            throw $e;
        }

        Response::json(message: 'System Initialized', code: 201, status: true, data: object(), bShouldExit: true);
    }


}
