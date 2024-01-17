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

$GLOBALS['TL_DCA']['tl_node']['palettes']['__selector__'][] = 'easyPopupSettings';
$GLOBALS['TL_DCA']['tl_node']['subpalettes']['easyPopupSettings'] = 'cssClass';

$GLOBALS['TL_DCA']['tl_node']['fields']['easyPopupSettings'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50', 'submitOnChange' => true],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_node']['fields']['cssClass'] = [
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

PaletteManipulator::create()
    ->addLegend('easy_popup_legend', 'name_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('easyPopupSettings', 'easy_popup_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_node')
;

if (InstalledVersions::isInstalled('oveleon/contao-component-style-manager')) {
    $GLOBALS['TL_DCA']['tl_node']['fields']['styleManager'] = [
        'inputType' => 'stylemanager',
        'eval' => ['tl_class' => 'clr stylemanager'],
        'sql' => 'blob NULL',
    ];

    PaletteManipulator::create()
        ->addField('styleManager', 'cssClass', PaletteManipulator::POSITION_BEFORE)
        ->applyToSubpalette('easyPopupSettings', 'tl_node')
    ;

    $GLOBALS['TL_DCA']['tl_node']['fields']['cssClass']['load_callback'][] = [StyleManager::class, 'onLoad'];
    $GLOBALS['TL_DCA']['tl_node']['fields']['cssClass']['save_callback'][] = [StyleManager::class, 'onSave'];
}
