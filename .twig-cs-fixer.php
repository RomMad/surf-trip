<?php

declare(strict_types=1);

use TwigCsFixer\Config\Config;
use TwigCsFixer\File\Finder;
use TwigCsFixer\Rules\File\DirectoryNameRule;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Standard\Symfony;
use TwigCsFixer\Standard\TwigCsFixer;

$finder = (new Finder())
    ->path('templates')
;

$ruleset = (new Ruleset())
    ->addStandard(new Symfony())
    ->addStandard(new TwigCsFixer())
    ->removeRule(DirectoryNameRule::class)
    ->addRule(new DirectoryNameRule(
        baseDirectory: 'templates',
        ignoredSubDirectories: ['bundles', 'components'],
        optionalPrefix: '_'
    ))
    ->addRule(new DirectoryNameRule(
        case: DirectoryNameRule::PASCAL_CASE,
        baseDirectory: 'templates/components'
    ))
;

return (new Config())
    ->setFinder($finder)
    ->setRuleset($ruleset)
    ->allowNonFixableRules()
    ->setCacheFile(__DIR__.'/var/.twig-cs-fixer.cache')
;
