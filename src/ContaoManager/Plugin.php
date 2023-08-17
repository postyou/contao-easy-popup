<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Postyou\ContaoEasyPopupBundle\PostyouContaoEasyPopupBundle;
use Terminal42\NodeBundle\Terminal42NodeBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(PostyouContaoEasyPopupBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, Terminal42NodeBundle::class]),
        ];
    }
}
