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
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Postyou\ContaoEasyPopupBundle\Popup\PopupManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule]
class EasyPopupController extends AbstractFrontendModuleController
{
    public function __construct(
        private readonly PopupManager $popupManager,
    ) {}

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $nodeId = (int) $model->popup;
        $popup = $this->popupManager->generate($nodeId);

        $template->set('popup', $popup);

        // Don't add the popup to the end of the body
        $GLOBALS['TL_BODY']['easy-popup-'.$nodeId] = '';

        return $template->getResponse();
    }
}
