<?php

use Contao\Image;
use Contao\System;
use Contao\Backend;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\CoreBundle\Security\ContaoCorePermissions;

$GLOBALS['TL_DCA']['tl_article']['list']['operations']['export'] = [
    'href'                  => 'act=export',
    'button_callback'       => array('tl_article_exporter', 'exportArticle'),
    'icon'                  => 'show.svg'
];

// class tl_article_exporter extends Backend
// {
//     /**
//      * Return the export article button
//      *
//      * @param array  $row
//      * @param string $href
//      * @param string $label
//      * @param string $title
//      * @param string $icon
//      * @param string $attributes
//      *
//      * @return string
//      */
//     public function exportArticle($row, $href, $label, $title, $icon, $attributes)
//     {
//         $objPage = PageModel::findById($row['pid']);

//         return System::getContainer()->get('security.helper')->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_THEME, $objPage->row()) ? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . StringUtil::specialchars($title) . '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)) . ' ';
//     }
// }
