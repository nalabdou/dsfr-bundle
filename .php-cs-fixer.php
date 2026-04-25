<?php

/*
 * This file is part of the nalabdou/dsfr-bundle package.
 *
 * (c) Nadim AL ABDOU <nadim.alabdou@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

if (!\file_exists(__DIR__ . '/src')) {
    exit(0);
}

$fileHeader = <<<'EOF'
 This file is part of the nalabdou/dsfr-bundle package.

 (c) Nadim AL ABDOU <nadim.alabdou@gmail.com>

 For the full copyright and license information, please view
 the LICENSE file that was distributed with this source code.

EOF;

return (new \PhpCsFixer\Config())
    ->setParallelConfig(\PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@PHP8x3Migration' => true,
        '@PHP8x3Migration:risky' => true,
        '@PHPUnit10x0Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,

        'declare_strict_types' => true,

        'header_comment' => [
            'header' => $fileHeader,
            'location' => 'after_declare_strict',
        ],

        'php_unit_attributes' => true,

        'void_return' => [
            'fix_lambda' => false,
        ],

        'native_function_invocation' => [
            'include' => ['@internal'],
            'scope' => 'namespaced',
            'strict' => true,
        ],
        'global_namespace_import' => [
            'import_classes' => false,
            'import_functions' => false,
            'import_constants' => false,
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        (new \PhpCsFixer\Finder())
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
    );
