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
use Contao\Database;

class PopupPickerProvider extends AbstractInsertTagPickerProvider implements DcaPickerProviderInterface
{
    public function getDcaTable(PickerConfig $config = null): string
    {
        return 'tl_node';
    }

    public function getDcaAttributes(PickerConfig $config): array
    {
        $attributes = ['fieldType' => 'radio'];

        if ($fieldType = $config->getExtra('fieldType')) {
            $attributes['fieldType'] = $fieldType;
        }

        if ($this->supportsValue($config)) {
            $attributes['value'] = array_map('intval', explode(',', $config->getValue()));
        }

        if (\is_array($rootNodes = $config->getExtra('rootNodes'))) {
            $attributes['rootNodes'] = $rootNodes;
        }

        $attributes['rootNodes'] = Database::getInstance()->prepare('SELECT id FROM tl_node WHERE pid IN (SELECT id FROM tl_node WHERE `type` = ?)')->execute('popup')->fetchEach('id');

        return $attributes;
    }

    public function convertDcaValue(PickerConfig $config, $value): string|int
    {
        return sprintf($this->getInsertTag($config), $value);
    }

    public function getName(): string
    {
        return 'nodePicker';
    }

    public function supportsContext($context): bool
    {
        return 'node' === $context || 'link' === $context;
    }

    public function supportsValue(PickerConfig $config): bool
    {
        foreach (explode(',', $config->getValue()) as $id) {
            if (!is_numeric($id)) {
                return false;
            }
        }

        return true;
    }

    protected function getRouteParameters(PickerConfig $config = null): array
    {
        return ['do' => 'nodes'];
    }

    protected function getDefaultInsertTag(): string
    {
        return '{{insert_node::%s}}';
    }
}
