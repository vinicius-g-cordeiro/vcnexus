#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../../../vendor/autoload.php';

$compiler = new Tools\SchemaCompiler\Compiler(
    new \App\Shared\Schema\Compiler\AttributeReader(),
    new \App\Shared\Schema\Compiler\ColumnResolver(),
    new Tools\SchemaCompiler\Generators\ModelGenerator(),
    new Tools\SchemaCompiler\Generators\MigrationGenerator(),
    new Tools\SchemaCompiler\MigrationSequence());

error_reporting(E_ALL ^E_DEPRECATED ^E_WARNING);


$directory = new RecursiveDirectoryIterator('schemas', FilesystemIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directory);
$files = new RegexIterator($iterator, '/Schema\.php$/');

foreach ($files as $file) {
    $relativePath = str_replace(__DIR__ . '/schemas/', '', $file->getPathname());
    $class = str_replace('/', '\\', str_replace(['schemas/', '.php'], ['App\Schemas\\', ''], $relativePath));
    if (is_subclass_of($class, \App\Schemas\AbstractSchema::class)) {
        $compiler->compile($class, $argv[1]);
    }
}