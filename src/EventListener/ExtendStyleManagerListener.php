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
use Oveleon\ContaoComponentStyleManager\Model\StyleManagerModel;

class ExtendStyleManagerListener
{
    /**
     * Find css groups using their table.
     */
    #[AsHook('styleManagerFindByTable')]
    public function onFindByTable(string $table, array $options = [])
    {
        if ('tl_node' === $table) {
            return StyleManagerModel::findBy(['extendNode=1'], null, $options);
        }

        return null;
    }

    /**
     * Check whether an element is visible for my dca in style manager widget.
     */
    #[AsHook('styleManagerIsVisibleGroup')]
    public function isVisibleGroup(StyleManagerModel $group, string $table): bool
    {
        if ('tl_node' === $table && (bool) $group->extendNode) {
            return true;
        }

        return false;
    }
}
