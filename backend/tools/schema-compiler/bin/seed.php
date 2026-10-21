#!/usr/bin/env php
<?php
/**
 * @brief Script used to initialize the system database through the command line
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionFactory;

require __DIR__ . '/../../../vendor/autoload.php';

// Require the ADODBExceptions
require_once __DIR__ . '/../../../vendor/adodb/adodb-php/adodb-exceptions.inc.php';

$databaseFactory = new ConnectionFactory();

$db = $databaseFactory->create();

if (!$db) {
    echo 'Could not connect to database';
    exit(1);
}

$directory = new RecursiveDirectoryIterator('database/migrations/tables/modules', FilesystemIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directory);
$tablesToDrop = new RegexIterator($iterator, '/[0-9]{3}_create_(.*?)_table.sql/');

// Drop all tables
foreach ($tablesToDrop as $file) {
    // remove the number padding 001_create_**_table keeping only the ** on the filename
    $tableName = preg_replace('/[0-9]{3}_create_(.*?)_table.sql/', '$1', basename($file->getPathname()));

    $db->Execute(sprintf('DROP TABLE IF EXISTS %s CASCADE', $tableName));

    echo sprintf("Dropped table %s\n", $tableName);
}

// $tableCreations = glob(__DIR__ . '/../../../database/migrations/tables/modules/**/*.sql') ;
$directory = new RecursiveDirectoryIterator('database/migrations/tables/modules', FilesystemIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directory);
$tableCreations = new RegexIterator($iterator, '/[0-9]{3}_create_(.*?)_table.sql/');

foreach ($tableCreations as $file) {
    $db->Execute(trim(file_get_contents($file->getPathname())));
    echo "\r\Executed " . basename($file->getPathname()) . "\n";
}

// $constraints = glob(__DIR__ . '/../../../database/migrations/constraints/modules/**/*.sql') ;
$directory = new RecursiveDirectoryIterator('database/migrations/constraints/modules', FilesystemIterator::SKIP_DOTS);
$iterator = new RecursiveIteratorIterator($directory);
$constraints = new RegexIterator($iterator, '/[0-9]{3}_constraints_(.*?)_fk.sql/');
foreach ($constraints as $file) {
    $db->Execute(trim(file_get_contents($file->getPathname())));

    echo "\r\Executed " . basename($file->getPathname()) . "\n";
}

echo "\n";

$user = trim(file_get_contents(getenv('DB_ROOT_USERNAME')));
$password = trim(file_get_contents(getenv('DB_ROOT_PASSWORD')));

$db = $databaseFactory->createSuperUser($user, $password);

if ($db) {

    // Find all the function on the folder functions on databse
    $directory = new RecursiveDirectoryIterator('database/functions', FilesystemIterator::SKIP_DOTS);
    $iterator = new RecursiveIteratorIterator($directory);
    $functions = new RegexIterator($iterator, '/[0-9]{3}_function_(.*?).sql/');
    foreach ($functions as $file) {
        $db->Execute(trim(file_get_contents($file->getPathname())));
        echo "\r\Executed " . basename($file->getPathname()) . "\n";
    }
}

echo "All migrations applied \n\n";
