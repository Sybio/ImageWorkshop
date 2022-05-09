<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php74\Rector\Assign\NullCoalescingOperatorRector;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return static function(RectorConfig $config): void {
    $config->paths([
        __DIR__ . '/src'
    ]);

    $config->import(SetList::DEAD_CODE);
    $config->import(SetList::TYPE_DECLARATION_STRICT);
    $config->import(SetList::TYPE_DECLARATION);
    $config->import(SetList::PHP_80);
    $config->import(SetList::PHP_74);
    $config->import(SetList::PHP_73);
    $config->import(SetList::EARLY_RETURN);
    $config->import(SetList::CODE_QUALITY);

    // register a single rule
    $config->rule(InlineConstructorDefaultToPropertyRector::class);
    $config->rule(RemoveExtraParametersRector::class);

    // define sets of rules
    $config->sets([
        LevelSetList::UP_TO_PHP_80
    ]);

    $config->phpstanConfig(__DIR__ . '/phpstan.neon');
};
