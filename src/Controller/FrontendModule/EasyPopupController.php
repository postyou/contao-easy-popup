<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\ModuleModel;
use Contao\Template;
use Postyou\ContaoEasyPopupBundle\Popup\PopupManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(template: 'frontend_module/easy_popup')]
class EasyPopupController extends AbstractFrontendModuleController
{
    public function __construct(
        private readonly PopupManager $popupManager,
    ) {
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $nodeId = (int) $model->popup;
        $popup = $this->popupManager->generate($nodeId);

        $template->popup = $popup;

        // Don't add the popup to the end of the body
        $GLOBALS['TL_BODY']['easy-popup-'.$nodeId] = '';

        return $template->getResponse();
    }
}
