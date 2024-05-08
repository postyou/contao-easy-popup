<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

$GLOBALS['TL_DCA']['tl_content']['palettes']['easy_popup'] = '
    {title_legend},type,headline;
    {include_legend},popup;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {invisible_legend:hide},invisible,start,stop;
    {expert_legend:hide},cssID
';

$GLOBALS['TL_DCA']['tl_content']['fields']['popup'] = [
    'inputType' => 'nodePicker',
    'eval' => ['mandatory' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql' => ['type' => 'blob', 'notnull' => false],
];
