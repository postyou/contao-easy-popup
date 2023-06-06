<?php

declare(strict_types=1);

/*
 * Node Bundle for Contao Open Source CMS.
 *
 * @copyright  Copyright (c) 2019, terminal42 gmbh
 * @author     terminal42 <https://terminal42.ch>
 * @license    MIT
 */

namespace Postyou\ContaoEasyPopup\Picker;

use Contao\CoreBundle\Picker\AbstractInsertTagPickerProvider;
use Contao\CoreBundle\Picker\DcaPickerProviderInterface;
use Contao\CoreBundle\Picker\PickerConfig;
use Contao\Database;
use Terminal42\NodeBundle\Model\NodeModel;

class NodePickerProvider extends AbstractInsertTagPickerProvider implements DcaPickerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDcaTable(PickerConfig $config = null): string
    {
        return 'tl_node';
    }

    /**
     * {@inheritdoc}
     */
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

        $attributes['rootNodes'] = Database::getInstance()->prepare("SELECT id FROM tl_node WHERE pid IN (SELECT id FROM tl_node WHERE `type` = ?)")->execute("popup")->fetchEach("id");



        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function convertDcaValue(PickerConfig $config, $value): string|int
    {
        return sprintf($this->getInsertTag($config), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'nodePicker';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsContext($context): bool
    {
        return 'node' === $context || 'link' === $context;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsValue(PickerConfig $config): bool
    {
        foreach (explode(',', $config->getValue()) as $id) {
            if (!is_numeric($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteParameters(PickerConfig $config = null): array
    {
        return ['do' => 'nodes'];
    }


    protected function getDefaultInsertTag(): string
    {
        return '{{insert_node::%s}}';
    }
}
