<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\Controller;

use Contao\ContentModel;
use Contao\CoreBundle\String\HtmlAttributes;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\StringUtil;
use Terminal42\NodeBundle\Model\NodeModel;
use Terminal42\NodeBundle\NodeManager;

trait EasyPopupFragment
{
    public function __construct(
        private readonly NodeManager $nodeManager,
    ) {}

    protected function prepareTemplate(FragmentTemplate &$template, ContentModel|ModuleModel $model): void
    {
        $nodeId = (int) $model->popup;
        $nodeModel = NodeModel::findOneBy(['id=?', 'type=?'], [$nodeId, NodeModel::TYPE_CONTENT]);

        $attrs = (new HtmlAttributes())
            ->setIfExists('data-timeout', $this->fromSerialized($nodeModel->popupTimeout))
            ->setIfExists('data-delay', $this->fromSerialized($nodeModel->popupDelay))
            ->setIfExists('data-show-on-leave', $nodeModel->showPopupOnLeave)
        ;

        $template->set('popup_attributes', $attrs);

        $template->set('popup', [
            ...$nodeModel->row(),
            'content' => $this->nodeManager->generateSingle($nodeId),
        ]);

        // Don't add the popup to the end of the body
        $GLOBALS['TL_BODY']['easy-popup-'.$nodeId] = '';
    }

    protected function fromSerialized(string $input): int
    {
        $input = StringUtil::deserialize($input, true);

        if (!isset($input['value'], $input['unit'])) {
            return 0;
        }

        $value = (int) $input['value'];

        return match ($input['unit']) {
            'hours' => $value * 60 * 60 * 1000,
            'minutes' => $value * 60 * 1000,
            'seconds' => $value * 1000,
            default => 0,
        };
    }
}
