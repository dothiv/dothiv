<?php

namespace Dothiv\BaseWebsiteBundle\Contentful;

use Dothiv\BaseWebsiteBundle\Exception\RuntimeException;
use Dothiv\ContentfulBundle\Adapter\ContentfulContentAdapter;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use Dothiv\ContentfulBundle\Item\ContentfulItem;
use Symfony\Component\Routing\RouterInterface;

class ViewBuilder
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var ContentfulContentAdapter
     */
    private $contentAdapter;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param string                   $defaultLocale
     * @param ContentfulContentAdapter $contentAdapter
     */
    public function __construct($defaultLocale, ContentfulContentAdapter $contentAdapter)
    {
        $this->contentAdapter = $contentAdapter;
        $this->defaultLocale  = $defaultLocale;
    }

    /**
     * @param ContentfulEntry $entry
     * @param string          $locale
     *
     * @return object
     */
    public function buildView(ContentfulEntry $entry, $locale)
    {
        $fields = $this->localize($entry, $locale);

        $view = $this->createView($fields, $locale);
        print_r($view);
        /*





        $pageData = array(
            'title'  => $page->title[$locale],
            'blocks' => array()
        );
        // TODO: Automate content discovery.
        $blocks = isset($page->blocks[$locale]) ? $page->blocks[$locale] : $page->blocks[$this->defaultLocale];
        foreach ($blocks as $block) {
            $blockEntry = $this->entryRepository->findNewestById($block['sys']['id']);
            $blockData  = array();
            foreach ($blockEntry->get()->getFields() as $k => $v) {
                $value         = isset($v[$locale]) ? $v[$locale] : $v[$this->defaultLocale];
                $blockData[$k] = $value;
            }
            // TODO: Automate.
            // TODO: Save assets locally.
            $imageEntry           = $this->assetRepository->findNewestById($blockData['image']['sys']['id'])->get();
            $blockData['image']   = array(
                'file'        => $imageEntry->file[$locale],
                'title'       => $imageEntry->title[$locale],
                'description' => $imageEntry->description[$locale]
            );
            $pageData['blocks'][] = $blockData;
        }
        #*/
        return $view;
    }

    protected function localize(ContentfulItem $entry, $locale)
    {
        $fields = array();
        foreach ($entry->getFields() as $k => $v) {
            $localValue = isset($v[$locale]) ? $v[$locale] : $v[$this->defaultLocale];
            $fields[$k] = $localValue;
        }
        return $fields;
    }

    /**
     * @param mixed  $value
     * @param string $locale
     *
     * @return mixed
     * @throws RuntimeException If a link cannot be resolved.
     */
    protected function getValue($value, $locale)
    {
        if (is_scalar($value)) {
            return $value;
        }
        if (is_array($value)) {
            if ($this->isLink($value)) {
                /** @var ContentfulItem $entry */
                $entry = $this->contentAdapter->findByTypeAndId($value['sys']['linkType'], $value['sys']['id'])->getOrCall(function () use ($value) {
                        throw new RuntimeException(
                            sprintf(
                                'Failed to fetch link %s:%s!',
                                $value['sys']['linkType'],
                                $value['sys']['id']
                            )
                        );
                    }
                );

                $fields               = $this->localize($entry, $locale);
                $fields['__itemType'] = $value['sys']['linkType'];
                $fields['__itemId']   = $value['sys']['id'];
                if ($entry instanceof ContentfulEntry) {
                    /** @var ContentfulEntry $entry */
                    $fields['__contentType'] = $this->contentAdapter->getContentTypeById($entry->getContentTypeId())->getName();
                }
                return $this->createView($fields, $locale);
            } else {
                $newValue = array();
                foreach ($value as $k => $v) {
                    $newValue[$k] = $this->getValue($v, $locale);
                }
                return $newValue;
            }
        }
    }

    protected function createView(array $fields, $locale)
    {
        $view = new \stdClass();
        foreach ($fields as $k => $v) {
            $view->$k = $this->getValue($v, $locale);
        }
        return $view;
    }

    protected function isLink(array $value)
    {
        if (!isset($value['sys'])) return false;
        $sys = $value['sys'];
        if (!isset($sys['type'])) return false;
        if ($sys['type'] != 'Link') return false;
        return true;
    }
} 
