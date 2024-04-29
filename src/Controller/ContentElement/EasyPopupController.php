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

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Postyou\ContaoEasyPopupBundle\Controller\EasyPopupFragment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement]
class EasyPopupController extends AbstractContentElementController
{
    use EasyPopupFragment;

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->prepareTemplate($template, $model);

        return $template->getResponse();
    }
}
