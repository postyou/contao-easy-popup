<?php

namespace Postyou\ContaoEasyPopup\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\CoreBundle\ContaoCoreBundle;
use Postyou\ContaoEasyPopup\ContaoEasyPopup;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoEasyPopup::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
