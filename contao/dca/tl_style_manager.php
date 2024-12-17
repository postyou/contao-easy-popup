<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

use Composer\InstalledVersions;
use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;

if (InstalledVersions::isInstalled('oveleon/contao-component-style-manager')) {
    Controller::loadDataContainer('tl_style_manager');

    $GLOBALS['TL_DCA']['tl_style_manager']['fields']['extendNode'] = [
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'clr'],
        'sql' => "char(1) NOT NULL default ''",
    ];

    PaletteManipulator::create()
        ->addField(['extendNode'], 'publish_legend', PaletteManipulator::POSITION_APPEND)
        ->applyToPalette('default', 'tl_style_manager')
    ;
}
