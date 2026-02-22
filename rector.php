<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\CodeQuality\Rector\MethodCall\LiteralGetToRequestClassConstantRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\ValueObject\PhpVersion;

// vendor/bin/rector process src --dry-run --xdebug

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_85);
    $rectorConfig->phpstanConfig(__DIR__.'/phpstan.dist.neon');
    $rectorConfig->parallel();

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_85,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SymfonySetList::SYMFONY_74,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        DoctrineSetList::TYPED_COLLECTIONS,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        DoctrineSetList::DOCTRINE_COLLECTION_22,
        DoctrineSetList::DOCTRINE_ORM_214,
        DoctrineSetList::DOCTRINE_BUNDLE_210,
        DoctrineSetList::GEDMO_ANNOTATIONS_TO_ATTRIBUTES,
    ]);

    $rectorConfig->skip([
        __DIR__.'/src/EventListener/ORM/LoggableListener.php',
        NewlineAfterStatementRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        ClosureToArrowFunctionRector::class,
        EncapsedStringsToSprintfRector::class,
        LiteralGetToRequestClassConstantRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(
        ClassPropertyAssignToConstructorPromotionRector::class,
        [
            'inline_public' => false,
            'rename_property' => true,
        ]
    );
};
