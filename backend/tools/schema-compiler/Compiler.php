<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace Tools\SchemaCompiler;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Compiler\{AttributeReader, ColumnResolver};
use Tools\SchemaCompiler\Generators\{ModelGenerator, MigrationGenerator};
use Tools\SchemaCompiler\MigrationSequence;

final class Compiler {

   public function __construct(
        private AttributeReader $attributeReader,
        private ColumnResolver $columnResolver,
        private ModelGenerator $modelGenerator,
        private MigrationGenerator $migrationGenerator,
        private MigrationSequence $sequence,

    ) {}

    /** @param class-string<AbstractSchema> $schemaClass */
    public function compile(string $schemaClass, ?string $typeToCompile = ''): void
    {
        $startTime = microtime(true);
        $schema = new $schemaClass();
        $columns = $this->columnResolver->resolveColumns($schemaClass); // reused as-is — same method built earlier

        $number = $this->sequence->numberFor($schema->table);
        $paddedNumber = str_pad((string) $number, 3, '0', STR_PAD_LEFT);
        
        $generatedSql = $this->migrationGenerator->generate($schema->table, $columns, $schemaClass); 

        if(empty($typeToCompile) || $typeToCompile === 'model') {
            $modelClass = $this->modelGenerator->deriveModelClass($schemaClass);
            $modelCode = $this->modelGenerator->generate($schemaClass, $schema, $columns);
            $modelPath = $this->modelGenerator->pathFromClass($modelClass);
            $this->writeFile($modelPath, $modelCode);
            echo "Compiled {$schemaClass} → {$modelPath}\n";
        }
        if(empty($typeToCompile) || $typeToCompile === 'migration') {
            // Generated SQL for migration and constraints
            $migrationPath = $this->migrationGenerator->deriveMigrationClass($schemaClass, $schema->table, 'create', $paddedNumber);
            $migrationSql = $generatedSql['create'];
            $this->writeFile($migrationPath, $migrationSql);

            echo "Compiled {$schemaClass} → {$migrationPath}\n";
        }
        if(empty($typeToCompile) || $typeToCompile === 'constraints') {
            $constraintsPath = $this->migrationGenerator->deriveMigrationClass($schemaClass, $schema->table, 'constraints', $paddedNumber);
            $constraintsSql = $generatedSql['constraints'];
            if(empty($constraintsSql) === false) {
                $this->writeFile($constraintsPath, $constraintsSql);
            }

            echo "Compiled {$schemaClass} → {$constraintsPath}\n";
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        echo "Finished compiling {$schemaClass}\n";
        echo "Execution time: {$executionTime} seconds\n";

    }

    private function writeFile(string $path, string $content): void
    {
        $dir = dirname($path);

        if (!is_dir($dir) && !mkdir($dir, 0755, recursive: true) && !is_dir($dir)) {
            throw new \RuntimeException("Failed to create directory: {$dir}");
        }

        if (file_put_contents($path, $content) === false) {
            throw new \RuntimeException("Failed to write file: {$path}");
        }
    }

}