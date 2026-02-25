<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new Finder())
    ->in(__DIR__)
    ->exclude([
        'config',
        'var',
    ])
;

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        'array_syntax' => ['syntax' => 'short'],
        'blank_line_before_statement' => [
            'statements' => ['continue', 'declare', 'exit', 'for', 'foreach', 'if', 'return', 'switch', 'throw', 'try'],
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'trait_import' => 'none',
                'case' => 'none',
            ],
        ],
        'comment_to_phpdoc' => true,
        'declare_strict_types' => true,
        'final_internal_class' => true,
        'fully_qualified_strict_types' => [
            'import_symbols' => true,
            'leading_backslash_in_global_namespace' => true,
        ],
        'modernize_types_casting' => true,
        'no_alias_functions' => true,
        'no_trailing_whitespace_in_string' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property',
                'construct',
                'magic',
                'method',
            ],
        ],
        'ordered_types' => [
            'sort_algorithm' => 'none',
            'null_adjustment' => 'always_last',
        ],
        'ordered_traits' => false,
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_to_param_type' => true,
        'phpdoc_to_property_type' => true,
        'phpdoc_to_return_type' => true,
        'phpdoc_types_order' => false,
        'php_unit_strict' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
        'psr_autoloading' => true,
        'self_accessor' => true,
        'static_lambda' => false,
        'strict_comparison' => true,
        'strict_param' => true,
        'void_return' => true,
    ])
    ->setFinder($finder)
    ->setLineEnding(\PHP_EOL)
    ->setCacheFile(__DIR__.'/var/cache/.php_cs.cache')
;
