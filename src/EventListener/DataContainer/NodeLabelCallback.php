<?php

declare(strict_types=1);

namespace Postyou\ContaoEasyPopupBundle\EventListener\DataContainer;

use Codefog\HasteBundle\Model\DcaRelationsModel;
use Codefog\TagsBundle\Manager\ManagerInterface;
use Contao\Backend;
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Intl\Locales;
use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;
use Terminal42\NodeBundle\Model\NodeModel;

class NodeLabelCallback
{
    public function __construct(
        private readonly Locales $locales,
        #[Autowire(service: 'codefog_tags.manager.terminal42_node')]
        private readonly ManagerInterface $tagsManager,
        private readonly TranslatorInterface $translator,
    ) {}

    #[AsCallback('tl_node', 'list.label.label', priority: 100)]
    public function addIcon(array $row, string $label, DataContainer $dc, string $imageAttribute = '', bool $returnImage = false, bool|null $isProtected = null): string
    {
        $published = $row['easyPopupSettings'] ? $row['popupPublished'] : true;

        $image = NodeModel::TYPE_CONTENT === $row['type']
            ? ($published ? 'articles.svg' : 'articles_1.svg')
            : 'folderC.svg';

        $imageAttribute .= ' data-icon="articles.svg" data-icon-disabled="articles_1.svg"';

        // Return the image only
        if ($returnImage) {
            return Image::getHtml($image, '', $imageAttribute);
        }

        $languages = [];
        $allLanguages = $this->locales->getLocales(null, true);

        // Generate the languages
        foreach (StringUtil::trimsplit(',', $row['languages']) as $language) {
            $languages[] = $allLanguages[$language] ?? $language;
        }

        $tags = [];
        $tagIds = DcaRelationsModel::getRelatedValues('tl_node', 'tags', $row['id']);

        // Generate the tags
        if (\count($tagIds) > 0) {
            /** @var Tag $tag */
            foreach ($this->tagsManager->getFilteredTags($tagIds) as $tag) {
                $tags[] = $tag->getName();
            }
        }

        $extras = [];

        if ([] !== $languages) {
            $extras[] = implode(', ', $languages);
        }

        if ([] !== $tags) {
            $extras[] = implode(', ', $tags);
        }

        if (NodeModel::TYPE_CONTENT === $row['type']) {
            $extras[] = \sprintf('ID: %d', $row['id']);

            if ($row['alias']) {
                $extras[] = \sprintf('%s: %s', $GLOBALS['TL_LANG']['tl_node']['alias'][0], $row['alias']);
            }
        }

        return \sprintf(
            '%s <a href="%s" title="%s">%s</a>%s',
            Image::getHtml($image, '', $imageAttribute),
            Backend::addToUrl('nn='.$row['id']),
            StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['selectNode']),
            $label,
            $extras ? ' '.implode('', array_map(static fn (string $v) => \sprintf('<span class="tl_gray" style="margin-left:3px;">[%s]</span>', $v), $extras)) : '',
        );
    }

    #[AsCallback('tl_node', 'list.operations.toggle.button')]
    public function toggleButton(DataContainerOperation $config): void
    {
        $record = $config->getRecord();

        if (NodeModel::TYPE_FOLDER === $record['type'] || !$record['easyPopupSettings']) {
            $config->setHtml('');

            return;
        }

        if (!$record['popupPublished']) {
            $config['icon'] = 'invisible.svg';
        }

        $label = [
            $this->translator->trans('tl_node.toggle.0', domain: 'contao_default'),
            $this->translator->trans('tl_node.toggle.1', domain: 'contao_default'),
            $this->translator->trans('tl_node.toggle.2', domain: 'contao_default'),
        ];

        $titleDisabled = sprintf($label[2], $record['id']);

        $config->setHtml('<a href="'.Backend::addToUrl($config['href'].'&amp;id='.$record['id']).'" title="'.StringUtil::specialchars($record['popupPublished'] ? $config['title'] : $titleDisabled).'" data-title="'.StringUtil::specialchars($config['title']).'" data-title-disabled="'.StringUtil::specialchars($titleDisabled).'" data-action="contao--scroll-offset#store" onclick="return AjaxRequest.toggleField(this,true)">'.Image::getHtml($config['icon'], $config['label'], 'data-icon="visible.svg" data-icon-disabled="invisible.svg" data-state="'.($record['popupPublished'] ? 1 : 0).'"').'</a> ');
    }
}
