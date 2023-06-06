<?php

namespace Postyou\ContaoEasyPopup\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\FormModel;
use Contao\FrontendTemplate;
use Contao\System;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * @Hook("parseFrontendTemplate")
 */
class ParseFrontendTemplateListener
{
    public function __invoke(string $buffer, string $templateName, FrontendTemplate $template): string
    {
        if ($templateName === "ce_hyperlink_popup") {
            $GLOBALS['TL_BODY'][] = \Contao\Template::generateScriptTag('bundles/contaoeasypopup/js/js_easy_popup.js', false);
        }

        return $buffer;
    }
}
