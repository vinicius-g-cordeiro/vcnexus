<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'var', 'bootstrap/cache', 'storage'])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();

return $config->setRules([
    // Aplica estritamente o padrão PSR-12 (regras de chaves, espaçamentos, etc.)
    '@PSR12' => true,

    // Boas práticas adicionais que complementam o desenvolvimento limpo
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    'no_unused_imports' => true,
    'clean_namespace' => true,
])
->setFinder($finder)
->setRiskyAllowed(false);
