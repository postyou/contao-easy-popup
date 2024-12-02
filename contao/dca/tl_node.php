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
$GLOBALS['TL_DCA']['tl_node']['subpalettes']['easyPopupSettings'] = 'popupDelay,popupTimeout,showPopupOnLeave,cssClass';
$GLOBALS['TL_DCA']['tl_node']['list']['label']['label_callback'] = ['tl_node', 'addIcon'];
// $GLOBALS['TL_DCA']['tl_node']['list']['sorting']['mode'] = 6;

$GLOBALS['TL_DCA']['tl_node']['list']['operations']['toggle'] = [
    'href'                => 'act=toggle&amp;field=published',
    'icon'                => 'visible.svg',
    'button_callback'     => array('tl_node', 'toggleIcon')
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

$GLOBALS['TL_DCA']['tl_node']['fields']['published'] = [
    'inputType' => 'checkbox',
    'toggle' => true,
    'eval' => ['tl_class' => 'w25 m12'],
    'sql' => ['type' => 'boolean', 'default' => true],
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

use Contao\CoreBundle\Security\ContaoCorePermissions;

class tl_node extends Backend
{



    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import(BackendUser::class, 'User');
    }


    public function addIcon($row, $label)
    {
        $image = 'articles';
        // $unpublished = ($row['start'] && $row['start'] > time()) || ($row['stop'] && $row['stop'] <= time());

        if (!$row['published']) {
            $image .= '_';
        }

        $attributes = sprintf(
            'data-icon="%s" data-icon-disabled="%s"',
            Image::getPath($row['published'] ? $image : rtrim($image, '_')),
            Image::getPath(rtrim($image, '_') . '_')
        );

        // $href = System::getContainer()->get('router')->generate('contao_backend_preview', array('page' => $row['pid'], 'article' => ($row['alias'] ?: $row['id'])));

        return '<a href="' . Backend::addToUrl('nn=' . $row['id']) . '" title="' . StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['view']) . '" target="_blank">' . Image::getHtml($image . '.svg', '', $attributes) . '</a> ' . $label;
    }

    /**
     * Return the "toggle visibility" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        // $security = System::getContainer()->get('security.helper');

        // if (!$security->isGranted(ContaoCorePermissions::USER_CAN_EDIT_FIELD_OF_TABLE, 'tl_node::invisible')) {
        //     return '';
        // }

        // // Disable the button if the element type is not allowed
        // if (!$security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_ELEMENT_TYPE, $row['type'])) {
        //     return Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
        // }

        $href .= '&amp;id=' . $row['id'];

        if (!$row['published']) {
            $icon = 'invisible.svg';
        }

        return '<a href="' . $this->addToUrl($href) . '" title="' . StringUtil::specialchars($title) . '" onclick="Backend.getScrollOffset();return AjaxRequest.toggleField(this,true)">' . Image::getHtml($icon, $label, 'data-icon="' . Image::getPath('visible.svg') . '" data-icon-disabled="' . Image::getPath('invisible.svg') . '" data-state="' . ($row['published'] ? 1 : 0) . '"') . '</a> ';
    }
}
