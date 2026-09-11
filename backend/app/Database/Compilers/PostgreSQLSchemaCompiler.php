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

use App\Database\Attributes\FunctionAtt;
use App\Database\Attributes\RowLevelSecurity;
use App\Shared\Connection;
use App\Database\Attributes\ForeignKeyConstraint;
use App\Database\Attributes\Index;
use App\Database\Attributes\UniqueConstraint;
use App\Database\Attributes\Policy;
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

    public function createPolicy(): string
    {
        $policy = '';

        foreach (Schema::policies($this->schema) as $attribute) {

            if (!isset($attribute->name)) {
                continue;
            }

            $policy .= "\r\n\r\n"
                . 'DROP POLICY IF EXISTS '
                . $attribute->name
                . ' ON public.'
                . $attribute->table
                . ";\r\n\r\n";

            $policy .= $this->buildPolicyDefinition($attribute);
        }

        return $policy . "\r\n";
    }




    public function buildPolicyDefinition(?Policy $policy): string
    {
        $definition = sprintf(
            "CREATE POLICY %s \r\n" .
            "ON public.%s \r\n" .
            "AS %s \r\n" .
            "%s \r\n" .
            "TO %s \r\n" .
            "USING ( %s ) ",
            $policy->name,
            $policy->table,
            $policy->restrictive === true
            ? 'RESTRICTIVE'
            : 'PERMISSIVE',
            isset($policy->withCheck)
            ? 'FOR ALL'
            : 'FOR ' . $policy->for,
            $policy->toUser,
            $this->buildPolicyUsing($policy->using)
        );

        if (isset($policy->withCheck)) {
            $definition .= sprintf(
                "\r\nWITH CHECK ( %s ) ",
                $this->buildPolicyWithCheck($policy->withCheck)
            );
        }

        return $definition . ";\r\n";
    }

    public function buildPolicyUsing(?array $usingArray): string
    {
        return $this->buildPolicyConditions($usingArray);
    }

    public function buildPolicyWithCheck(?array $withCheckArray): string
    {
        return $this->buildPolicyConditions($withCheckArray);
    }
    private function buildPolicyConditions(?array $conditions): string
    {
        if (empty($conditions)) {
            return '';
        }

        $expressions = [];

        foreach ($conditions as $key => $value) {

            $setting = sprintf(
                "current_setting('%s', true)",
                $value['key']
            );

            /*
             * NULLIF(current_setting(...), '')
             */
            if (($value['nullIf'] ?? false) === true) {
                $setting = sprintf(
                    "NULLIF(%s, '')",
                    $setting
                );
            }

            /*
             * Cast
             *
             * ::bigint
             * ::varchar[]
             * ::integer[]
             * etc.
             */
            if (!empty($value['type'])) {
                $setting .= '::' . $value['type'];
            }

            /*
             * Array condition
             *
             * Example:
             *
             * '1' = ANY(current_setting('app.roles', true)::varchar[])
             */
            if (($value['array'] ?? false) === true) {

                $condition = $this->quotePolicyValue(
                    $value['condition']
                );

                $operator = $value['operator'] ?? '= ANY';

                $expressions[] = sprintf(
                    '%s %s(%s)',
                    $condition,
                    $operator,
                    $setting
                );

                continue;
            }

            /*
             * Normal boolean condition
             *
             * current_setting('app.role', true) = '1'
             */
            if (
                isset($value['condition']) &&
                $value['condition'] !== ''
            ) {
                $expressions[] = sprintf(
                    '%s = %s',
                    $setting,
                    $this->quotePolicyValue($value['condition'])
                );

                continue;
            }

            /*
             * Column comparison
             *
             * tenant_id = NULLIF(
             *     current_setting('app.tenant_id', true),
             *     ''
             * )::bigint
             */
            if ($key !== '') {
                $expressions[] = sprintf(
                    '%s = %s',
                    $key,
                    $setting
                );
            }
        }

        return implode(" OR \r\n", $expressions);
    }


    private function quotePolicyValue(mixed $value): string
    {
        return "'" . str_replace("'", "''", (string) $value) . "'";
    }

    public function createRowLevelSecurity(): string
    {
        $rls = '';
        foreach (Schema::rls($this->schema) as $attribute) {
            if (!isset($attribute->table))
                continue;

            $rls .= ($attribute->forced === false ? $this->buildRowLevelSecurityDefinition($attribute) : $this->buildForcedRowLevelSecurityDefinition($attribute)) . ";\r\n";
        }

        return $rls;
    }

    public function buildRowLevelSecurityDefinition(?RowLevelSecurity $rowLevelSecurity): string
    {
        $definition = sprintf('ALTER TABLE public.%s ENABLE ROW LEVEL SECURITY', $rowLevelSecurity->table);
        return $definition;
    }

    public function buildForcedRowLevelSecurityDefinition(?RowLevelSecurity $rowLevelSecurity): string
    {
        $definition = sprintf('ALTER TABLE public.%s FORCE ROW LEVEL SECURITY', $rowLevelSecurity->table);
        return $definition;
    }

    private function buildColumnDefinition(?object $column): string
    {
        $definition = sprintf('"%s" %s', $column->name, $column->type);

        if ($column->inherit === false) { // if we should not inherit we just return an empty string 
            return '';
        }

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

    private function createFunctions(): string
    {
        $definitions = [];
        $functions = '';
        foreach (Schema::functionAtt($this->schema) as $attribute) {
            if (!isset($attribute->name))
                continue;

            $functions .= 'DROP FUNCTION IF EXISTS ' . $attribute->name . ';' . "\r\n\r\n";
            $functions .= $this->buildFunctionQuery($attribute);
        }

        return $functions . "\r\n";
    }

    private function buildFunctionQuery(?FunctionAtt $function): string
    {
        $query = $function->tsql;

        return trim($query . ";", ';');
    }

    public function initDefaultsUsers()
    {
        $sqlAdminPassword = password_hash(trim(file_get_contents(trim(getenv('ADMIN_PASSWORD')))), PASSWORD_BCRYPT, ['cost' => 12]);

        $response = $this->getConnection()->Execute(
            "SELECT set_config('app.tenant_id', ?, false), set_config('app.user_id', ?, false);",
            ['1', '1']
        );

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
        } finally {
            $this->getConnection()->Execute("SELECT set_config('app.tenant_id', '', false), set_config('app.user_id', '', false);");
        }


        $sql = "
insert
    into
    public.tenant_users
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
    role,
    roles,
    permissions,
    user_id)
values(uuidv7(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,null, null, null, null, null, null, 1, 1, 'SuperAdministrator', 'Admin', '', array['administrator'::character varying(100)], '1999-04-23', 'vinismtpgo@gmail.com', '', 0, 0, 0, 0, null, null, null, null, null, '', 1, array['1'::character varying(100)], array['users.view'::character varying(100)], 1);
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
        } finally {
            $this->getConnection()->Execute("SELECT set_config('app.tenant_id', '', false), set_config('app.user_id', '', false);");
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
        } finally {
            $this->getConnection()->Execute("SELECT set_config('app.tenant_id', '', false), set_config('app.user_id', '', false);");
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
values(uuidv7(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null, null, null, null, 1, 0, 0, 1, 1, 'VCNexus (MEI)', 'VCNexus', '', 1, '62.728.369/0001-72', '', '', 'cerradogstudio.viniciuscordeiro@gmail.com', 'https://www.vcnexus.com.br',array['+55 61 9 9179-5618'::character varying(20)],'{10}');
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
        } finally {
            $this->getConnection()->Execute("SELECT set_config('app.tenant_id', '', false), set_config('app.user_id', '', false);");
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
    active,
    \"name\",
    \"password\",
    \"username\",
    email,
    phone,
    \"blocked\",
    blocked_by,
    blocked_at)
values(uuidv7(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,null, null, null, null, null, null, 1, 'SuperAdministrator', '" . $sqlAdminPassword . "', 'administrator.vcnexus', 'vinismtpgo@gmail.com', '+55 61 9 9179-5618', null, null, null);
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
        } finally {
            $this->getConnection()->Execute("SELECT set_config('app.tenant_id', '', false), set_config('app.user_id', '', false);");
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
        } finally {
            $this->getConnection()->Execute("SELECT set_config('app.tenant_id', '', false), set_config('app.user_id', '', false);");
        }


        $this->getConnection()->StartTrans();
        try {
            $sql = $this->createPolicy();
            $sql .= $this->createRowLevelSecurity();

            $this->getConnection()->Execute($sql);

            if ($this->getConnection()->HasFailedTrans()) {
                throw new \RuntimeException('Failed to add the RLS and policies');
            }

            $this->getConnection()->CompleteTrans();
        } catch (Throwable $e) {
            Response::log(data: $e);
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();
            throw $e;
        } finally {
            $this->getConnection()->Execute("SELECT set_config('app.tenant_id', '', false), set_config('app.user_id', '', false);");
        }






        Response::json(message: 'System Initialized', code: 201, status: true, data: object(), bShouldExit: true);
    }


}
