<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Symfony\Component\Asset\Packages;

#[AsCallback('tl_node', 'config.onload')]
class AddBackendAssetsCallback
{
    public function __construct(
        private readonly Packages $packages,
    ) {}

    public function __invoke(): void
    {
        $GLOBALS['TL_CSS'][] = $this->packages->getUrl('backend.css', 'postyou_contao_easy_popup');
    }
}
