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
use Contao\Template;
use Symfony\Component\Asset\Packages;
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
        protected readonly Packages $packages,
    ) {
    }

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
        $delay = $this->getTimeFromInputUnit($nodeModel->popupDelay);

        $attrs = [];

        if ($timeout = $this->getTimeFromInputUnit($nodeModel->popupTimeout)) {
            $attrs[] = 'data-timeout='.$timeout;
        }

        if (($delay = $this->getTimeFromInputUnit($nodeModel->popupDelay)) !== false) {
            $attrs[] = 'data-delay='.$delay;
        }

        if ($nodeModel->showPopupOnLeave) {
            $attrs[] = 'data-show-on-leave';
        }

        $popup = $this->twig->render('@Contao/easy_popup/popup.html.twig', [
            ...$nodeModel->row(),
            'content' => $this->nodeManager->generateSingle($nodeId),
            'popup_attributes' => \implode(' ', $attrs),
        ]);

        $GLOBALS['TL_BODY']['easy_popup_js'] = Template::generateScriptTag($this->packages->getUrl('easy-popup.js', 'postyou_contao_easy_popup'));

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
