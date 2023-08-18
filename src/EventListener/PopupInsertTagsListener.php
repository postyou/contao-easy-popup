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

    public function __construct(
        protected readonly PopupManager $popupManager,
    ) {
    }

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
        $popup = $this->popupManager->getPopup($nodeId);

        $k = array_key_last($tags);
        $tags[$k] = str_replace('</body>', "{$popup}</body>", $tags[$k]);

        return '#easy-popup-'.$nodeId;
    }
}
