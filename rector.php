<?php

declare(strict_types=1);

use Contao\Rector\Set\ContaoLevelSetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        'contao',
        'src',
    ])
    ->withComposerBased(symfony: true, twig: true, doctrine: true)
    ->withSets([
        ContaoLevelSetList::UP_TO_CONTAO_53
    ]);
