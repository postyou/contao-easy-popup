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

use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
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
        protected readonly TokenChecker $tokenChecker,
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

        $popup = '';
        $columns = ['id=?', 'type=?'];

        if (!$this->tokenChecker->isPreviewMode()) {
            $columns[] = '((easyPopupSettings=1 AND published=1) OR easyPopupSettings=0)';
        }

        $nodeModel = NodeModel::findOneBy($columns, [$nodeId, NodeModel::TYPE_CONTENT]);

        if (null !== $nodeModel) {
            $delay = $this->getTimeFromInputUnit($nodeModel->popupDelay);

            $attrs = (new HtmlAttributes())
                ->setIfExists('data-timeout', $this->getTimeFromInputUnit($nodeModel->popupTimeout))
                ->set('data-delay', $delay, $delay >= 0)
                ->setIfExists('data-show-on-leave', $nodeModel->showPopupOnLeave)
            ;

            $popup = $this->twig->render('@Contao/easy_popup/popup.html.twig', [
                ...$nodeModel->row(),
                'content' => $this->nodeManager->generateSingle($nodeId),
                'popup_attributes' => $attrs,
            ]);
        }

        self::$popupCache[$nodeId] = $popup;
        unset(self::$locked[$nodeId]);

        return $popup;
    }

    protected function getTimeFromInputUnit(string $input): int|false
    {
        $input = StringUtil::deserialize($input, true);

        if (!isset($input['value'], $input['unit']) || !is_numeric($input['value'])) {
            return false;
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
