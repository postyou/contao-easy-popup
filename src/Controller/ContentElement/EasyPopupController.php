<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\Controller\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Template;
use Postyou\ContaoEasyPopupBundle\Popup\PopupManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

#[AsContentElement(template: 'content_element/easy_popup')]
class EasyPopupController extends AbstractContentElementController
{
    public function __construct(
        private readonly PopupManager $popupManager,
        private readonly ScopeMatcher $scopeMatcher,
        private readonly RouterInterface $router,
    ) {
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $nodeId = (int) $model->popup;

        if ($this->scopeMatcher->isBackendRequest($request)) {
            $wildcard = new BackendTemplate('be_wildcard');
            $wildcard->wildcard = '### ' . $GLOBALS['TL_LANG']['CTE']['easy_popup'][0] . ' ###';
            $wildcard->id = $nodeId;
            $wildcard->link = 'Popup';
            $wildcard->href = $this->router->generate('contao_backend', array('do' => 'nodes', 'table' => 'tl_content', 'id' => $nodeId));

            $template->as_editor_view = true;
            $template->wildcard = $wildcard->parse();

            return $template->getResponse();
        }

        $popup = $this->popupManager->generate($nodeId);

        $template->popup = $popup;

        // Don't add the popup to the end of the body
        $GLOBALS['TL_BODY']['easy-popup-'.$nodeId] = '';

        return $template->getResponse();
    }
}
