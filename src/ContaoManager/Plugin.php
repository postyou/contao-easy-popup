<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) John Doe
 *
 * @license LGPL-3.0-or-later
 */

namespace Postyou\ContaoEasyPopup\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Postyou\ContaoEasyPopup\ContaoEasyPopup;
use Terminal42\NodeBundle\Terminal42NodeBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoEasyPopup::class)
                ->setLoadAfter([ContaoCoreBundle::class, Terminal42NodeBundle::class]),
        ];
    }
}
