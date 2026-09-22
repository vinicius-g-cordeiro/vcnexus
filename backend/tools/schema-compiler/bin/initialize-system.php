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


$databaseFactory = new ConnectionFactory();

$db = $databaseFactory->create();

if(!$db) {
    echo 'Could not connect to database';
    exit(1);
}

echo 'Do you want to drop all tables and re-create them? This will delete all data. Are you sure? (y/n)';
$res = trim(fgets(STDIN));

if($res !== 'y') {
    echo 'User canceled, exiting';
    exit(1);
}
else{
    // Get table names from .sqls
    $tablesToDrop = glob(__DIR__ . '/../../../database/migrations/tables/modules/**/*.sql') ;
    
    // Drop all tables
    foreach($tablesToDrop as $file) {
        // remove the number padding 001_create_**_table keeping only the ** on the filename
        $tableName = preg_replace('/[0-9]{3}_create_(.*?)_table.sql/', '$1', basename($file));
        
        $db->Execute(sprintf('DROP TABLE IF EXISTS %s CASCADE', $tableName));
        
        echo sprintf("Dropped table %s\n", $tableName);
    }
}

$tableCreations = glob(__DIR__ . '/../../../database/migrations/tables/modules/**/*.sql') ;
foreach($tableCreations as $file) {
    $db->Execute(trim(file_get_contents($file)));
}

$constraints = glob(__DIR__ . '/../../../database/migrations/constraints/modules/**/*.sql') ;
foreach($constraints as $file) {
    $db->Execute(trim(file_get_contents($file)));
}

echo "\n";

echo 'Do you want to run the initial function to seed the database, with super user? (y/n)';
$res = trim(fgets(STDIN));

if($res === 'y') {
    echo 'Enter super user: ';
    $user = trim(fgets(STDIN));

    echo 'Enter password: ';
    $password = getPassword(true);
    echo "\n";

    // connect to the database with super user
    $db = $databaseFactory->createSuperUser($user, $password);

    if($db) {
        // Find the function_get_user_tenants.sql
        $file = __DIR__ . '/../../../database/functions/function_get_user_tenants.sql';
        $db->Execute(trim(file_get_contents($file)));

        // find the function_get_tenant_membershipt_access.sql
        $file = __DIR__ . '/../../../database/functions/function_get_tenant_membership_access.sql';
        $db->Execute(trim(file_get_contents($file)));


        // find the function_initilize_system.sql
        $file = __DIR__ . '/../../../database/functions/function_initialize_system.sql';
        $db->Execute(trim(file_get_contents($file)));
    }
    
}

echo "All migrations applied\n";