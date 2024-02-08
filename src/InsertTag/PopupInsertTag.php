<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\InsertTag;

use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\Exception\InvalidInsertTagException;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Terminal42\NodeBundle\Model\NodeModel;
use Terminal42\NodeBundle\NodeManager;
use Twig\Environment;

#[AsInsertTag('popup_url')]
class PopupInsertTag
{
    /**
     * @var array<int, bool>
     */
    private static $popupCache = [];

    public function __construct(
        protected readonly Environment $twig,
        protected readonly NodeManager $nodeManager,
    ) {}

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        $nodeId = $insertTag->getParameters()->getScalar(0);

        if (null === $nodeId) {
            throw new InvalidInsertTagException('Missing parameters for popup insert tag.');
        }

        if (!\is_int($nodeId)) {
            throw new InvalidInsertTagException(sprintf('Invalid node id %s for popup insert tag.', $nodeId));
        }

        $this->addPopupToPage($nodeId);

        return new InsertTagResult('#easy-popup-'.$nodeId);
    }

    private function addPopupToPage(int $nodeId): void
    {
        if (isset(self::$popupCache[$nodeId])) {
            return;
        }

        // Cache the id, so the popup is only generated once
        self::$popupCache[$nodeId] = true;

        $nodeModel = NodeModel::findOneBy(['id=?', 'type=?'], [$nodeId, NodeModel::TYPE_CONTENT]);

        // Add popup to the end of the page
        $GLOBALS['TL_BODY'][] = $this->twig->render('@Contao/easy_popup/popup.html.twig', [
            ...$nodeModel->row(),
            'content' => $this->nodeManager->generateSingle($nodeId),
        ]);
    }
}
