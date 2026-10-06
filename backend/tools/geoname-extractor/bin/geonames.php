<?php

declare(strict_types=1);


use Tools\GeonameExtractor\Generators\ImportGeoNames;
use App\Infrastructure\Database\ConnectionFactory;

require __DIR__ . '/../../../vendor/autoload.php';


/// =============================================================================
/// THIS SCRIPT SHOULD GET THE .sql FILES FROM THE MIGRATIONS TABLE, IN ORDER, THIS IS
/// WHY IT'S SET TO GET FOR EXAMPLE: TENANT FIRST, THEN THE REST. SO IT HAS THE TABLES
/// =============================================================================

/**
 * Get a password from the shell.
 *
 * This function works on *nix systems only and requires shell_exec and stty.
 *
 * @param  boolean $stars Wether or not to output stars for given characters
 * @return string
 */
function getPassword($stars = false)
{
    // Get current style
    $oldStyle = shell_exec('stty -g');

    if ($stars === false) {
        shell_exec('stty -echo');
        $password = rtrim(fgets(STDIN), "\n");
    } else {
        shell_exec('stty -icanon -echo min 1 time 0');

        $password = '';
        while (true) {
            $char = fgetc(STDIN);

            if ($char === "\n") {
                break;
            } else if (ord($char) === 127) {
                if (strlen($password) > 0) {
                    fwrite(STDOUT, "\x08 \x08");
                    $password = substr($password, 0, -1);
                }
            } else {
                fwrite(STDOUT, "*");
                $password .= $char;
            }
        }
    }

    // Reset old style
    shell_exec('stty ' . $oldStyle);

    // Return the password
    return $password;
}



$connectionFactory = new ConnectionFactory();

echo 'Enter super user: ';
$user = trim(fgets(STDIN));

echo 'Enter password: ';
$password = getPassword(true);
echo "\n";

// connect to the database with super user
$db = $connectionFactory->createSuperUser($user, $password);

echo 'Connected to the database.' . PHP_EOL;

$importer = new ImportGeoNames($db);

$importer->run();