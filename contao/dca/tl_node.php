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
use Contao\ArrayUtil;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Oveleon\ContaoComponentStyleManager\StyleManager\StyleManager;

$GLOBALS['TL_DCA']['tl_node']['palettes']['__selector__'][] = 'easyPopupSettings';
$GLOBALS['TL_DCA']['tl_node']['subpalettes']['easyPopupSettings'] = 'popupDelay,popupTimeout,showPopupOnLeave,cssClass,popupPublished';

ArrayUtil::arrayInsert($GLOBALS['TL_DCA']['tl_node']['list']['operations'], -1, 'toggle');

$GLOBALS['TL_DCA']['tl_node']['fields']['popupPublished'] = [
    'inputType' => 'checkbox',
    'toggle' => true,
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => true],
];

$GLOBALS['TL_DCA']['tl_node']['fields']['easyPopupSettings'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50', 'submitOnChange' => true],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_node']['fields']['popupTimeout'] = [
    'inputType' => 'inputUnit',
    'options' => ['hours', 'minutes', 'seconds'],
    'reference' => &$GLOBALS['TL_LANG']['tl_node']['popup']['timeUnits'],
    'eval' => ['rgxp' => 'digit', 'tl_class' => 'w25'],
    'sql' => ['type' => 'string', 'length' => 255, 'notnull' => true, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_node']['fields']['popupDelay'] = [
    'inputType' => 'inputUnit',
    'options' => ['hours', 'minutes', 'seconds'],
    'reference' => &$GLOBALS['TL_LANG']['tl_node']['popup']['timeUnits'],
    'eval' => ['rgxp' => 'digit', 'tl_class' => 'w25'],
    'sql' => ['type' => 'string', 'length' => 255, 'notnull' => true, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_node']['fields']['showPopupOnLeave'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w25 m12'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$GLOBALS['TL_DCA']['tl_node']['fields']['cssClass'] = [
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w25 clr'],
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
