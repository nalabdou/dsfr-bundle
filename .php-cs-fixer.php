<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRules([
        '@Symfony'           => true,
        '@Symfony:risky'     => true,
        '@PHP83Migration'    => true,
        'declare_strict_types' => true,
        'strict_param'       => true,
        'ordered_imports'    => ['sort_algorithm' => 'alpha'],
        'no_unused_imports'  => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
