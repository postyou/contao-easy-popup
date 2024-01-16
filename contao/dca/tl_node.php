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
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Oveleon\ContaoComponentStyleManager\StyleManager\StyleManager;

$GLOBALS['TL_DCA']['tl_node']['fields']['cssClass'] = [
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

PaletteManipulator::create()
    ->addLegend('expert_legend')
    ->addField('cssClass', 'expert_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_node')
;

if (InstalledVersions::isInstalled('oveleon/contao-component-style-manager')) {
    $GLOBALS['TL_DCA']['tl_node']['fields']['styleManager'] = [
        'inputType' => 'stylemanager',
        'eval' => ['tl_class' => 'clr stylemanager'],
        'sql' => 'blob NULL',
    ];

    $GLOBALS['TL_DCA']['tl_node']['config']['onload_callback'][] = [StyleManager::class, 'addPalette'];
    $GLOBALS['TL_DCA']['tl_node']['fields']['cssClass']['load_callback'][] = [StyleManager::class, 'onLoad'];
    $GLOBALS['TL_DCA']['tl_node']['fields']['cssClass']['save_callback'][] = [StyleManager::class, 'onSave'];
}
