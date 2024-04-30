<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\Popup;

use Contao\CoreBundle\String\HtmlAttributes;
use Contao\StringUtil;
use Terminal42\NodeBundle\Model\NodeModel;
use Terminal42\NodeBundle\NodeManager;
use Twig\Environment;

class PopupManager
{
    /**
     * @var array<int, bool>
     */
    private static $locked = [];

    /**
     * @var array<int, string>
     */
    private static $popupCache = [];

    public function __construct(
        protected readonly Environment $twig,
        protected readonly NodeManager $nodeManager,
    ) {}

    public function generate(int $nodeId): string
    {
        if (isset(self::$popupCache[$nodeId])) {
            return self::$popupCache[$nodeId];
        }

        if (isset(self::$locked[$nodeId])) {
            return '';
        }

        self::$locked[$nodeId] = true;

        $nodeModel = NodeModel::findOneBy(['id=?', 'type=?'], [$nodeId, NodeModel::TYPE_CONTENT]);

        $attrs = (new HtmlAttributes())
            ->setIfExists('data-timeout', $this->getTimeFromInputUnit($nodeModel->popupTimeout))
            ->setIfExists('data-delay', $this->getTimeFromInputUnit($nodeModel->popupDelay))
            ->setIfExists('data-show-on-leave', $nodeModel->showPopupOnLeave)
        ;

        $popup = $this->twig->render('@Contao/easy_popup/popup.html.twig', [
            ...$nodeModel->row(),
            'content' => $this->nodeManager->generateSingle($nodeId),
            'popup_attributes' => $attrs,
        ]);

        self::$popupCache[$nodeId] = $popup;
        unset(self::$locked[$nodeId]);

        return $popup;
    }

    protected function getTimeFromInputUnit(string $input): int
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
