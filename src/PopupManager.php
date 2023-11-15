<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle;

use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\FrontendTemplate;
use Terminal42\NodeBundle\NodeManager;

class PopupManager
{
    public function __construct(
        protected readonly NodeManager $nodeManager,
        protected readonly InsertTagParser $insertTagParser,
    ) {}

    public function getPopup(int $nodeId): string
    {
        $popup = new FrontendTemplate('easy_popup');

        $popup->id = $nodeId;
        $popup->content = $this->nodeManager->generateSingle($nodeId) ?? '';

        return $this->insertTagParser->replace($popup->parse());
    }
}
