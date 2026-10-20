<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';


// get the arguments
$arguments = $argv;

$allowedOptions = [
    'init' => [
        'tools/schema-compiler/bin/compile.php',
        'tools/schema-compiler/bin/seed.php',
        'tools/geoname-extractor/bin/geonames.php',
    ],
    'compile' => ['tools/schema-compiler/bin/compile.php'],
    'seed' => ['tools/schema-compiler/bin/seed.php'],
    'geonames' => ['tools/geoname-extractor/bin/geonames.php'],
];

$options = [];

// check if any arguments were provided
if (count($arguments) === 1 || empty($arguments)) {
    $options['init'] = $allowedOptions['init'];
}

foreach ($arguments as $argument => $value) {
    if (preg_match('/^--(.*)$/', $value, $matches)) {
        if (isset($allowedOptions[$matches[1]])) {
            $options[$matches[1]] = $allowedOptions[$matches[1]];
        }
    }

    
}

// run the commands in order
$commandsOrder = [
    'init',
    'compile',
    'seed',
    'geonames',
];

uasort($options, function ($a, $b) use ($commandsOrder) {
    return array_search($a, $commandsOrder) - array_search($b, $commandsOrder);
});

$initialTime = microtime(true);
foreach ($options as $option => $command) {
    if(preg_match('/^init$/', $option)) {
        foreach ($command as $com) {
            echo "\n" . sprintf('Running the %s command', $com) . "\n";
            try{
                echo exec('php ' . $com);
                
            }catch (Exception $e) {
                echo $e->getTraceAsString() . "\n";
                echo sprintf('Execution took %s seconds', round(microtime(true) - $initialTime, 2)) . "\n";
                exit(1);
            }
        }
        // as the init command runs all the commands, we are allowed to break out of it, and don't execute any other of the commands
        break;
    }
    if ($command) {
        try{
            if(is_array($command)) {
                foreach ($command as $com) {
                    echo "\n" . sprintf('Running the %s command', $com) . "\n";
                    echo exec('php ' . $com);
                    
                }
            }else{
                echo sprintf('Running the %s command', $command) . "\n";
                echo exec('php ' . $command);
            }
        }catch (Exception $e) {
            echo $e->getTraceAsString() . "\n";
            echo sprintf('Execution took %s seconds', round(microtime(true) - $initialTime, 2)) . "\n";
            exit(1);
        }
    }

}


echo sprintf('Total execution time %s seconds', round(microtime(true) - $initialTime, 2)) . "\n";
exit(0);