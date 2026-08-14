<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
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
    $rectorConfig->configure()->withComposerBased(
        twig: true,
        doctrine: true,
        phpunit: true,
        symfony: true,
    );

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_85,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        DoctrineSetList::TYPED_COLLECTIONS,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        DoctrineSetList::GEDMO_ANNOTATIONS_TO_ATTRIBUTES,
    ]);

    $rectorConfig->skip([
        __DIR__.'/src/EventListener/ORM/LoggableListener.php',
        NewlineAfterStatementRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        ClosureToArrowFunctionRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(
        ClassPropertyAssignToConstructorPromotionRector::class,
        [
            'inline_public' => false,
            'rename_property' => true,
        ]
    );
};
