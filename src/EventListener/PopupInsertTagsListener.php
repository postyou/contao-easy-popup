<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Postyou\ContaoEasyPopupBundle\PopupManager;

#[AsHook('replaceInsertTags')]
class PopupInsertTagsListener
{
    public const TAG = 'popup_url';

    private static $popupCache = [];

    public function __construct(
        protected readonly PopupManager $popupManager,
    ) {}

    /**
     * @param array<string> $flags
     * @param array<string> $tags
     * @param array<string> $cache
     */
    public function __invoke(string $insertTag, bool $useCache, string $cachedValue, array $flags, array &$tags, array $cache, int $_rit, int $_cnt): bool|string
    {
        $chunks = explode('::', $insertTag);

        if (self::TAG !== $chunks[0]) {
            return false;
        }

        $nodeId = (int) $chunks[1];
        $value = '#easy-popup-'.$nodeId;

        if (isset(self::$popupCache[$nodeId])) {
            return $value;
        }

        // Cache the id, so the popup is only generated once
        // Has to be done first to prevent infinite loops, if the popup contains the insert tag
        self::$popupCache[$nodeId] = true;

        $popup = $this->popupManager->getPopup($nodeId);

        // Add popup to the end of the page
        $GLOBALS['TL_BODY'][] = $popup;

        return $value;
    }
}
