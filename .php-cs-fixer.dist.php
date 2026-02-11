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
    ])
    ->setFinder($finder)
    ->setLineEnding(\PHP_EOL)
    ->setCacheFile(__DIR__.'/var/cache/.php_cs.cache')
;
