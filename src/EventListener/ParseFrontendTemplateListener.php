<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\FrontendTemplate;

/**
 * @Hook("parseFrontendTemplate")
 */
class ParseFrontendTemplateListener
{
    public function __invoke(string $buffer, string $templateName, FrontendTemplate $template): string
    {
        if ('ce_hyperlink_popup' === $templateName) {
            $GLOBALS['TL_BODY'][] = \Contao\Template::generateScriptTag('bundles/contaoeasypopup/js/js_easy_popup.js', false);
        }

        return $buffer;
    }
}
