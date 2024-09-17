<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ECSConfig): void {
    $ECSConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // import SetList here on purpose to avoid overridden by existing Skip Option in current config
    $ECSConfig->sets([SetList::PSR_12, SetList::SYMPLIFY, SetList::COMMON, SetList::CLEAN_CODE]);
    $ECSConfig->skip([
        NotOperatorWithSuccessorSpaceFixer::class,
        CastSpacesFixer::class,
        BinaryOperatorSpacesFixer::class,
        UnaryOperatorSpacesFixer::class,
    ]);
};
