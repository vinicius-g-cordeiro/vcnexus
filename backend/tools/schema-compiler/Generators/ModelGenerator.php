<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace Tools\SchemaCompiler\Generators;

use App\Schemas\AbstractSchema;

final class ModelGenerator
{
    /** @param \App\Shared\Schema\Attributes\Column[] $columns */
    public function generate(string $schemaClass, AbstractSchema $schema, array $columns): string
    {
        $modelClass = $this->deriveModelClass($schemaClass);
        [$namespace, $className] = $this->splitClass($modelClass);

        $properties = array_map(fn($col) => $this->renderProperty($col), $columns);
        $propertiesFromArray = array_map(fn($col) => $this->renderPropertyFromArray($col), $columns);

        $date = new \DateTime();
        $year = new \DateTime()->format('Y');
        $date = $date->format('d/m/Y');
        $projectVersion = getenv('APP_VERSION');
        return <<<PHP
        <?php
        /**
         * GENERATED from {$schemaClass} — do not edit directly. Edit the schema and recompile.
         * @brief 
         * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
         * @version {$projectVersion}
         * @date {$date}
         * @copyright Copyright (c) {$year} - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
         */
        
        declare(strict_types=1);

        namespace {$namespace};

        use App\Shared\Schema\BaseModel;

        final class {$className} extends BaseModel
        {
            {$this->join($properties, ";\n    ")};
            public function __construct() {}

            public function toArray(): array
            {
                return get_object_vars(\$this);
            }

            public static function fromArray(array \$data): self
            {
                \$newObject = new static();
                {$this->join($propertiesFromArray, ";\n        ")};
                return \$newObject;
            }
        }
        PHP;
    }

    private function renderProperty($column): string
    {
        $type = $this->sqlToPhpType($column->type);
        $type = $column->nullable ? "?{$type}" : $type;
        
        // For the model the primary keys should be considered nullable, in order to use the model without specifing the keys, for example when inserting a new record on the database
        if(in_array($column->name,['uuid', 'active']) || ($column->name === 'id' && $type === 'int')) {
            $type = "?{$type}";
        }
        return "public {$type} \${$column->name}";
    }

    public function renderPropertyFromArray($column): string
    {
        $type = $this->sqlToPhpType($column->type);
        $type = $column->nullable ? "?{$type}" : $type;
        
        if(in_array($type, ['int', '?int'])) {
            return "\$newObject->{$column->name} = isset(\$data['{$column->name}']) ? (int) \$data['{$column->name}'] : null";
        }

        if(in_array($type, ['array', '?array'])) {
            return "\$newObject->{$column->name} = isset(\$data['{$column->name}']) ? (array) \$data['{$column->name}'] : null";
        }

        return "\$newObject->{$column->name} = isset(\$data['{$column->name}']) ? \$data['{$column->name}'] : null";
    }

    public function deriveModelClass(string $schemaClass): string
    {
        // Authentication\UserSchema  →  Authentication\Models\User
        $withoutSchemaSuffix = preg_replace('/Schema$/', '', $schemaClass);
        $modelClass = str_replace('App\\Schemas\\', 'App\\Modules\\', $withoutSchemaSuffix);

        // Insert "Models\" right before the class name
        $parts = explode('\\', $modelClass);
        $className = array_pop($parts);
        $parts[] = 'Models';
        $parts[] = $className;

        return implode('\\', $parts);
    }

    private function splitClass(string $fqcn): array
    {
        $parts = explode('\\', $fqcn);
        $className = array_pop($parts);
        $namespace = implode('\\', $parts);

        return [$namespace, $className];
    }

    /**
     * Maps a SQL/schema-level type to the PHP scalar type used in the generated Model's constructor.
     */
    private function sqlToPhpType(string $sqlType): string
    {
        return match ($sqlType) {
            'varchar', 'text', 'char', 'uuid', 'timestamp', 'timestamptz', 'date' => 'string',
            'smallint', 'integer', 'bigint' => 'int',
            'numeric', 'decimal', 'float', 'double' => 'float',
            'boolean' => 'bool',
            'jsonb', 'json' => 'array',
            default => 'string',
        };
    }

    /**
     * PascalCase / snake_case column name → camelCase PHP property name.
     * e.g. 'token_expires_at' → 'tokenExpiresAt'
     */
    private function toCamelCase(string $snakeCaseName): string
    {
        $parts = explode('_', $snakeCaseName);
        $first = array_shift($parts);
        $rest = array_map('ucfirst', $parts);

        return $first . implode('', $rest);
    }

    /**
     * Converts an absolute file-system path back to a fully-qualified class name,
     * assuming a PSR-4 mapping of App\ → src/.
     */
    public function classFromPath(string $path): string
    {
        $relative = str_replace(['src/', '.php'], '', $path);
        return 'App\\' . str_replace('/', '\\', $relative);
    }

    /**
     * Converts a fully-qualified class name to its expected file path under src/,
     * creating parent directories if they don't exist yet (first compile of a new module).
     */
    public function pathFromClass(string $fqcn): string
    {
        $path = 'src/' . str_replace(['App\\', '\\'], ['', '/'], $fqcn) . '.php';

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, recursive: true);
        }

        return $path;
    }

    /**
     * Small formatting helper — joins generated property lines with the given separator,
     * indenting each continuation line to match the constructor's opening parenthesis.
     */
    private function join(array $lines, string $separator): string
    {
        return implode($separator, $lines);
    }
}