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
        try {
            $response = $this->getConnection()->Execute($creationQuery);
        } catch (Throwable $th) {
            throw new DatabaseNotCreatedException($th->getMessage(), $th->getCode(), $th->getPrevious());
        }

        if($tableName == 'users'){
            $this->initDefaultsUsers();
        }

        return Response::json(message: '', code: 204, data: object(DatabaseCreated: $response !== false));
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
            $sql .= ' ON UPDATE NO ACTION ';
        }

        if (isset($constraint->actionOnDelete) && $constraint->actionOnDelete === true) {
            $sql .= ' ON DELETE ' . $constraint->deleteAction;
        }

        if ($constraint->deferred === true) {
            $sql .= ' DEFERRABLE INITIALLY DEFERRED ';
        }
        // if (isset($constraint->deferred) && $constraint->deferred === true) {
        //     $sql .= ' DEFERRABLE INITIALLY DEFERRED ';
        // }

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


        $sqlAdminPassword = password_hash(trim(file_get_contents(trim(getenv('ADMIN_PASSWORD')))), PASSWORD_BCRYPT, ['cost' => 16]);
        

        $sql = "
INSERT INTO public.tenants (\"name\", modules, active) VALUES('Cerrado G Studios', ARRAY['0'::character varying(4)], 1);

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
    last_agent)
values(uuidv4(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, null,null, null, null, null, null, null, null, 1, 'admin', '".$sqlAdminPassword."', 'super', '', array[''::character varying(100)], '2026-09-03', 'vinismtpgo@gmail.com', '', 0, 0, 0, 0, 0, 0, null, 0, null, '', null, null, null, '');


insert into public.usernames (\"username\", active, user_id, created_by) VALUES('admin', 1, 1, 1)";


        $this->getConnection()->StartTrans();

        try {
            $result = $this->getConnection()->Execute($sql);

            if ($this->getConnection()->HasFailedTrans()) {
                throw new \RuntimeException('Transaction failed.');
            }

            $this->getConnection()->CompleteTrans();

            return $result;

        } catch (Throwable $e) {
            $this->getConnection()->FailTrans();
            $this->getConnection()->CompleteTrans();

            throw $e;
        }

    }

}
