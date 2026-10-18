<?php

declare(strict_types=1);


use Tools\GeonameExtractor\Generators\ImportGeoNames;
use App\Infrastructure\Database\ConnectionFactory;

require __DIR__ . '/../../../vendor/autoload.php';


$connectionFactory = new ConnectionFactory();

$user = trim(file_get_contents(getenv('DB_ROOT_USERNAME')));
$password = trim(file_get_contents(getenv('DB_ROOT_PASSWORD')));

// connect to the database with super user
$db = $connectionFactory->createSuperUser($user, $password);

echo 'Connected to the database.' . PHP_EOL;

$importer = new ImportGeoNames($db);

$importer->run();