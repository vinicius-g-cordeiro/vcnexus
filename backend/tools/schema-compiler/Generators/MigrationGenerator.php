<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace Tools\SchemaCompiler\Generators;

use App\Shared\Schema\Attributes\{Column, ForeignKey, Index};
use App\Shared\Schema\Compiler\AttributeReader;

final class MigrationGenerator
{
    public string $table = "";
    /** @param Column[] $columns */
    public function generate(string $table, array $columns, string $schemaClass): array
    {

        $this->table = $table;
        $lines = [sprintf("CREATE TABLE %s (", $table)];

        foreach ($columns as $column) {
            $lines[] = sprintf("    %s,", $this->renderColumnSql($column));
        }

        // Get the last column and remove the comma
        $lines[count($lines) - 1] = substr($lines[count($lines) - 1], 0, -1);

        $lines[] = ");";

        foreach (AttributeReader::getRowLevelSecurity($schemaClass) as $rowLevelSecurity) {
            if(!$rowLevelSecurity->forced) {
                $lines[] = sprintf('ALTER TABLE %s ENABLE ROW LEVEL SECURITY;', $rowLevelSecurity->table);
            }else{
                $lines[] = sprintf('ALTER TABLE %s FORCE ROW LEVEL SECURITY;', $rowLevelSecurity->table);
            }
        }

        


        foreach (AttributeReader::getPolicies($schemaClass) as $policy) {
            $definition = sprintf(
                "CREATE POLICY %s " .
                "ON public.%s " .
                "AS %s " .
                "%s " .
                "TO %s " .
                "USING ( %s ) ",
                $policy->name,
                $policy->table,
                $policy->restrictive === true
                ? 'RESTRICTIVE'
                : 'PERMISSIVE', 
                (isset($policy->withCheck)
                ? 'FOR ALL'
                : 'FOR ' . $policy->for),
                $policy->toUser,
                $this->buildPolicyUsing($policy->using)
            );

            if (isset($policy->withCheck)) {
                $definition .= sprintf(
                    "WITH CHECK ( %s ) ",
                    $this->buildPolicyWithCheck($policy->withCheck)
                );
            }

            $lines[] = $definition . ";";
        }
        $lines[] = $this->buildCommentsQuery($schemaClass);

        $constraints = [];
        // In MigrationGenerator, after the column lines:
        foreach (AttributeReader::getUniqueConstraints($schemaClass) as $unique) {
            $constraints[] = sprintf("ALTER TABLE %s ADD CONSTRAINT %s UNIQUE (%s);", $table, $unique->name, implode(', ', $unique->columns));
        }

        $constraints[] = $this->buildIndexesQuery($schemaClass);


        foreach(AttributeReader::getForeignKeys($schemaClass) as $foreignKey){
            $constraints[] = $this->buildForeignKeyConstraint($foreignKey);
        }

        
        foreach(AttributeReader::getPrimaryKeys($schemaClass) as $primaryKey){
            $constraints[] = sprintf("ALTER TABLE %s ADD PRIMARY KEY (%s);", $table, implode(', ', $primaryKey->key));
        }
        
        return [
            'create' => implode("\n", $lines),
            'constraints' => implode("\n", $constraints)
        ];
    }

    private function renderColumnSql($column): string
    {
        $sql = "{$column->name} " . $this->pgType($column);
        if($column->identity === true && $column->identity_generated != ''){
            $sql .= " {$column->identity_generated}";
        }
        if (!$column->nullable)
            $sql .= " NOT NULL";

        if(isset($column->identity) === false){
            if($column->default !== null && $column->default !== ''){
                if((is_string($column->default) && strpos($column->default, '(') === false && preg_match('/^current_timestamp/i', $column->default) === false) 
                    or (is_string($column->default) && preg_match('/[-]/', $column->default))) {
                    $sql .= " DEFAULT '{$column->default}'";
                }else {
                    $sql .= " DEFAULT {$column->default}";
                }
            }else if($column->default === ''){
                $sql .= " DEFAULT ''";
            }
        }

        
        if (isset($column->primary_key) && $column->primary_key !== false){

            
            $sql .= " PRIMARY KEY ";
            
            if(is_array($column->primary_key)){
                $sql .= " (" . implode(', ', $column->primary_key) . ")";
            }else{
                if(is_string($column->primary_key)){
                    if($column->primary_key !== 'id'){    
                        $sql .= " (" . $column->primary_key . ")";
                    }
                }
            }
        }

        if($column->autoincrement === true){
            $sql .= " AUTOINCREMENT";
        }

        if ($column->references){
            $column->referencesColumns = implode(', ', $column->referencesColumns);
            $sql .= " REFERENCES {$column->references} ({$column->referencesColumns})";
            if(isset($column->referencesDeleteAction)){
                $sql .= " ON DELETE {$column->referencesDeleteAction}";
            }
        }
        return $sql;
    }

    /**
     * Maps the schema-level column type (from #[Column(type: '...')]) to actual PostgreSQL SQL,
     * folding in length/precision/scale where the type needs them.
     */
    private function pgType(object $column): string
    {
        return match ($column->type) {
            'varchar' => $column->length ? "varchar({$column->length})" : 'varchar',
            'char' => "char({$column->length})",
            'text' => 'text',
            'uuid' => 'uuid',
            'smallint' => 'smallint',
            'integer' => 'integer',
            'bigint' => 'bigint',
            'numeric' => ($column->precision && $column->scale)
            ? "numeric({$column->precision}, {$column->scale})"
            : 'numeric',
            'boolean' => 'boolean',
            'timestamp' => 'timestamp',
            'timestamptz' => 'timestamptz',
            'date' => 'date',
            'jsonb' => 'jsonb',
            'json' => 'json',
            'UUID' => 'uuid',
            default => throw new \InvalidArgumentException("Unmapped column type: {$column->type}"),
        };
    }

    /**
     * Resolves a Model/Schema class to its table name for use in a REFERENCES clause.
     * Reads the referenced class's own #[Column]-less "table" property, same as AbstractSchema::$table.
     */
    private function tableFor(string $referencedClass): string
    {
        // The referenced class is usually a Model; walk back to its Schema counterpart to read ->table,
        // since the Model itself doesn't carry the table name (only the Schema does).
        $schemaClass = $this->modelClassToSchemaClass($referencedClass);
        $schema = new $schemaClass();

        return $schema->table;
    }

    /**
     * Inverse of ModelGenerator::deriveModelClass() — given a Model class, find its originating Schema class.
     * e.g. App\Modules\People\Models\Customer → App\Schemas\People\CustomerSchema
     */
    private function modelClassToSchemaClass(string $modelClass): string
    {
        $parts = explode('\\', $modelClass);
        $className = array_pop($parts);   // 'Customer'
        array_pop($parts);                 // drop 'Models'
        $moduleNamespace = implode('\\', $parts); // 'App\Modules\People'

        $schemaNamespace = str_replace('App\\Modules\\', 'App\\Schemas\\', $moduleNamespace);

        return "{$schemaNamespace}\\{$className}Schema";
    }

    /**
     * Renders one CREATE UNIQUE constraint line per #[Unique(name:, columns:)] found on the schema class.
     */
    private function renderUniqueConstraints(string $table, array $uniqueConstraints): array
    {
        return array_map(
            fn($unique) => "ALTER TABLE {$table} ADD CONSTRAINT {$unique->name} UNIQUE (" . implode(', ', $unique->columns) . ");",
            $uniqueConstraints
        );
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

        return implode(" OR ", $expressions);
    }


    private function quotePolicyValue(mixed $value): string
    {
        return "'" . str_replace("'", "''", (string) $value) . "'";
    }


    public function buildPolicyUsing(?array $usingArray): string
    {
        return $this->buildPolicyConditions($usingArray);
    }

    public function buildPolicyWithCheck(?array $withCheckArray): string
    {
        return $this->buildPolicyConditions($withCheckArray);
    }


    private function buildForeignKeyConstraint(ForeignKey $constraint): string
    {
        $sql = sprintf('ALTER TABLE "%s" ADD CONSTRAINT "%s"  FOREIGN KEY (%s) REFERENCES "%s" (%s)', $this->table, $constraint->name, $this->quoteColumns($constraint->foreignKeys), $constraint->references, $this->quoteColumns($constraint->columns));

        if (isset($constraint->actionOnUpdate) && $constraint->actionOnUpdate === false) {
            $sql .= ' ON UPDATE NO ACTION';
        }

        if (isset($constraint->deleteAction) && $constraint->deleteAction !== '') {
            $sql .= ' ON DELETE ' . $constraint->deleteAction;
        }

        if ($constraint->deferred === true) {
            $sql .= ' DEFERRABLE INITIALLY DEFERRED ';
        }

        return $sql . ";";
    }

    private function buildIndexesQuery($schemaClass): string
    {
        $queries = [];

        foreach (AttributeReader::indexes($schemaClass) as $index) {
            $queries[] = $this->buildIndexQuery($index);
        }

        return implode("", $queries);
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

        return $sql . ";";
    }

    private function buildIndexCondition(array $conditions)
    {
        $conditionParts = [];
        foreach ($conditions as $column => $value) {
            $conditionParts[] = sprintf('"%s" = %s', $column, $value);
        }

        return implode(' AND ', $conditionParts);
    }


    private function buildCommentsQuery(string $schemaName): string
    {
        $comments = [];

        foreach (AttributeReader::getComments($schemaName) as $attribute) {
            if (!isset($attribute->comment) || $attribute->comment === '')
                continue;

            // escape single quotes
            $comment =  str_replace("'", "''", $attribute->comment);
            $comments[] = sprintf("COMMENT ON COLUMN \"%s\".\"%s\" IS '%s';", $this->table, $attribute->name, $comment);
        }

        return implode("\n", $comments);
    }


    private function quoteColumns(array $columns): string
    {
        return implode(',', array_map(static function (string $column): string {
            return '"' . $column . '"';
        }, $columns));
    }

    /**
     * Schemas\Authentication\UserSchema → database\Migrations\Authentication\
     */
    public function deriveMigrationClass(string $schemaClass, string $tableName, string $type, string $paddedNumber): string
    {
        // Authentication\UserSchema  →  Authentication\Models\User
        $withoutSchemaSuffix = preg_replace('/Schema$/', '', $schemaClass);
        $migrationClass = match($type){
            'create' => $migrationClass = mb_strtolower(str_replace('App\\Schemas\\', 'database\\migrations\\tables\\modules\\', $withoutSchemaSuffix)),
            'constraints' => $migrationClass = mb_strtolower(str_replace('App\\Schemas\\', 'database\\migrations\\constraints\\modules\\', $withoutSchemaSuffix))
        };

        // Insert "migrations\" right before the class name
        $parts = explode('\\', $migrationClass);
        // Remove the class name
        unset($parts[count($parts) - 1]);

        $fileName = match($type){
            'create' => mb_strtolower(sprintf('%s_create_%s_table.sql', $paddedNumber, $tableName), 'UTF-8'),
            'constraints' => mb_strtolower(sprintf('%s_constraints_%s_fk.sql', $paddedNumber, $tableName), 'UTF-8')
        };
        $parts[] = $fileName;
        
        return implode('/', $parts);
    }


    /**
     * Converts a fully-qualified class name to its expected file path under src/,
     * creating parent directories if they don't exist yet (first compile of a new module).
     */
    public function pathFromClass(string $fqcn): string
    {
        $path = 'database/migrations/' . str_replace(['App\\', '\\'], ['', '/'], $fqcn) . '.php';

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, recursive: true);
        }

        return $path;
    }




}