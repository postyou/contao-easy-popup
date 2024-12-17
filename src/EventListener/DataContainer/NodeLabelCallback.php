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

    #[AsCallback('tl_node', 'list.label.label')]
    public function addIcon(array $row, string $label, DataContainer $dc, string $imageAttribute = '', bool $returnImage = false, bool|null $isProtected = null): string
    {
        $image = NodeModel::TYPE_CONTENT === $row['type']
            ? ($row['published'] ? 'articles.svg' : 'articles_1.svg')
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
            $languages[] = $allLanguages[$language];
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

        return sprintf(
            '%s <a href="%s" title="%s">%s</a>%s%s',
            Image::getHtml($image, '', $imageAttribute),
            Backend::addToUrl('nn='.$row['id']),
            StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['selectNode']),
            $label,
            \count($languages) > 0 ? sprintf(' <span class="tl_gray" style="margin-left:3px;">[%s]</span>', implode(', ', $languages)) : '',
            \count($tags) > 0 ? sprintf(' <span class="tl_gray" style="margin-left:3px;">[%s]</span>', implode(', ', $tags)) : '',
        );
    }

    #[AsCallback('tl_node', 'list.operations.toggle.button')]
    public function toggleButton(DataContainerOperation $config): void
    {
        $record = $config->getRecord();

        if (NodeModel::TYPE_FOLDER === $record['type']) {
            $config->setHtml('');

            return;
        }

        if (!$record['published']) {
            $config['icon'] = 'invisible.svg';
        }

        $label = [
            $this->translator->trans('tl_node.toggle.0', domain: 'contao_default'),
            $this->translator->trans('tl_node.toggle.1', domain: 'contao_default'),
            $this->translator->trans('tl_node.toggle.2', domain: 'contao_default'),
        ];

        $titleDisabled = sprintf($label[2], $record['id']);

        $config->setHtml('<a href="'.Backend::addToUrl($config['href'].'&amp;id='.$record['id']).'" title="'.StringUtil::specialchars($record['published'] ? $config['title'] : $titleDisabled).'" data-title="'.StringUtil::specialchars($config['title']).'" data-title-disabled="'.StringUtil::specialchars($titleDisabled).'" data-action="contao--scroll-offset#store" onclick="return AjaxRequest.toggleField(this,true)">'.Image::getHtml($config['icon'], $config['label'], 'data-icon="visible.svg" data-icon-disabled="invisible.svg" data-state="'.($record['published'] ? 1 : 0).'"').'</a> ');
    }
}
