<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\Picker;

use Contao\CoreBundle\Picker\AbstractInsertTagPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;

class PopupPickerProvider extends AbstractInsertTagPickerProvider implements DcaPickerProviderInterface
{
    public function getName(): string
    {
        return 'popupPicker';
    }

    public function supportsContext($context): bool
    {
        return 'link' === $context;
    }

    public function supportsValue(PickerConfig $config): bool
    {
        return $this->isMatchingInsertTag($config);
    }

    public function getDcaTable(?PickerConfig $config = null): string
    {
        return 'tl_node';
    }

    public function getDcaAttributes(PickerConfig $config): array
    {
        $attributes = ['fieldType' => 'radio'];

        if ($this->supportsValue($config)) {
            $attributes['value'] = $this->getInsertTagValue($config);

            if ($flags = $this->getInsertTagFlags($config)) {
                $attributes['flags'] = $flags;
            }
        }

        return $attributes;
    }

    /**
     * @param int $value
     */
    public function convertDcaValue(PickerConfig $config, $value): int|string
    {
        return sprintf($this->getInsertTag($config), $value);
    }

    protected function getRouteParameters(?PickerConfig $config = null): array
    {
        return ['do' => 'nodes'];
    }

    protected function getDefaultInsertTag(): string
    {
        return '{{popup_url::%s}}';
    }
}
