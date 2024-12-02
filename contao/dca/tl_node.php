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
$GLOBALS['TL_DCA']['tl_node']['list']['operations']['toggle'] = [
    'href'                => 'act=toggle&amp;field=published',
    'icon'                => 'visible.svg',
    'button_callback'     => array('tl_node', 'toggleIcon')
];

$GLOBALS['TL_DCA']['tl_node']['fields']['published'] = [
    'inputType' => 'checkbox',
    'toggle' => true,
    'eval' => ['tl_class' => 'w25 m12'],
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

use Contao\CoreBundle\Security\ContaoCorePermissions;

class tl_node extends Contao\Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import(Contao\BackendUser::class, 'User');
    }


    public function addIcon($row, $label)
    {
        $sub = 0;
        // $unpublished = ($row['start'] && $row['start'] > time()) || ($row['stop'] && $row['stop'] <= time());

        if (!$row['published']) {
            ++$sub;
        }

        if ($row['protected']) {
            $sub += 2;
        }

        $image = 'articles.svg';

        if ($sub > 0) {
            $image = 'articles_' . $sub . '.svg';
        }

        $attributes = sprintf(
            'data-icon="%s" data-icon-disabled="%s"',
            $row['protected'] ? 'articles_2.svg' : 'articles.svg',
            $row['protected'] ? 'articles_3.svg' : 'articles_1.svg',
        );

        // $href = Contao\System::getContainer()->get('router')->generate('contao_backend_preview', array('page' => $row['pid'], 'article' => ($row['alias'] ?: $row['id'])));

        return '<a href="' . Contao\Backend::addToUrl('nn=' . $row['id']) . '" title="' . Contao\StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['view']) . '" target="_blank">' . Contao\Image::getHtml($image, '', $attributes) . '</a> ' . $label;
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

        $href .= '&id=' . $row['id'];

        if (!$row['published']) {
            $icon = 'invisible.svg';
        }

        return '<a href="' . $this->addToUrl($href) . '" title="' . Contao\StringUtil::specialchars($title) . '" onclick="Backend.getScrollOffset();return AjaxRequest.toggleField(this,true)">' . Contao\Image::getHtml($icon, $label, 'data-icon="' . Contao\Image::getPath('visible.svg') . '" data-icon-disabled="' . Contao\Image::getPath('invisible.svg') . '" data-state="' . ($row['published'] ? 1 : 0) . '"') . '</a> ';
    }
}
