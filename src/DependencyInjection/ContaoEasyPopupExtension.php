<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) John Doe
 *
 * @license LGPL-3.0-or-later
 */

namespace Postyou\ContaoEasyPopup\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContaoEasyPopupExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
