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
use Postyou\ContaoEasyPopupBundle\Popup\PopupManager;

#[AsInsertTag('popup_url')]
class PopupInsertTag
{
    public function __construct(
        protected readonly PopupManager $popupManager,
    ) {}

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        $nodeId = $insertTag->getParameters()->getScalar(0);

        if (null === $nodeId) {
            throw new InvalidInsertTagException('Missing parameters for popup insert tag.');
        }

        if (!\is_int($nodeId)) {
            throw new InvalidInsertTagException(\sprintf('Invalid node id %s for popup insert tag.', $nodeId));
        }

        $this->addPopupToPage($nodeId, 'easy-popup-'.$nodeId);

        return new InsertTagResult('#easy-popup-'.$nodeId);
    }

    private function addPopupToPage(int $nodeId, string $key): void
    {
        if (\array_key_exists($key, $GLOBALS['TL_BODY'] ?? [])) {
            return;
        }

        // Add popup to the end of the page
        $GLOBALS['TL_BODY'][$key] = $this->popupManager->generate($nodeId);
    }
}
