<?php

namespace Dothiv\BaseWebsiteBundle\Contentful;

use Dothiv\BaseWebsiteBundle\BaseWebsiteBundleEvents;
use Dothiv\BaseWebsiteBundle\Event\ContentfulViewEvent;
use Dothiv\BaseWebsiteBundle\Exception\RuntimeException;
use Dothiv\ContentfulBundle\Adapter\ContentfulContentAdapter;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use Dothiv\ContentfulBundle\Item\ContentfulItem;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param string                   $defaultLocale
     * @param ContentfulContentAdapter $contentAdapter
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($defaultLocale, ContentfulContentAdapter $contentAdapter, EventDispatcherInterface $dispatcher)
    {
        $this->contentAdapter = $contentAdapter;
        $this->defaultLocale  = $defaultLocale;
        $this->dispatcher     = $dispatcher;
    }

    /**
     * @param ContentfulEntry $entry
     * @param string          $locale
     *
     * @return object
     */
    public function buildView(ContentfulEntry $entry, $locale)
    {
        $spaceId                         = $entry->getSpaceId();
        $fields                          = $this->localize($entry, $locale);
        $fields['cfMeta']                = array();
        $fields['cfMeta']['url']         = $entry->getContentfulUrl();
        $fields['cfMeta']['contentType'] = $this->contentAdapter->getContentTypeById($spaceId, $entry->getContentTypeId())->getName();
        $fields['cfMeta']['itemName']    = $entry->getName();
        $view                            = $this->createView($fields, $spaceId, $locale);
        return $view;
    }

    /**
     * @param ContentfulItem $entry
     * @param                $locale
     *
     * @return array
     */
    protected function localize(ContentfulItem $entry, $locale)
    {
        $fields = array();
        foreach ($entry->getFields() as $k => $v) {
            $localValue = isset($v[$locale]) ? $v[$locale] : $v[$this->defaultLocale];
            if (trim($localValue) === "") {
                continue;
            }
            $fields[$k] = $localValue;
        }
        return $fields;
    }

    /**
     * @param mixed  $value
     * @param string $spaceId
     * @param string $locale
     *
     * @return mixed
     * @throws RuntimeException If a link cannot be resolved.
     */
    protected function getValue($value, $spaceId, $locale)
    {
        if (is_scalar($value)) {
            return $value;
        }
        if (is_array($value)) {
            if ($this->isLink($value)) {
                /** @var ContentfulItem $entry */
                $entry = $this->contentAdapter->findByTypeAndId($spaceId, $value['sys']['linkType'], $value['sys']['id'])->getOrCall(function () use ($value) {
                        throw new RuntimeException(
                            sprintf(
                                'Failed to fetch link %s:%s!',
                                $value['sys']['linkType'],
                                $value['sys']['id']
                            )
                        );
                    }
                );

                $fields                       = $this->localize($entry, $locale);
                $fields['cfMeta']             = array();
                $fields['cfMeta']['itemType'] = $value['sys']['linkType'];
                $fields['cfMeta']['itemId']   = $value['sys']['id'];
                $fields['cfMeta']['url']      = $entry->getContentfulUrl();
                if ($entry instanceof ContentfulEntry) {
                    /** @var ContentfulEntry $entry */
                    $fields['cfMeta']['contentType'] = $this->contentAdapter->getContentTypeById($spaceId, $entry->getContentTypeId())->getName();
                    $fields['cfMeta']['itemName']    = $entry->getName();
                }
                if ($entry instanceof ContentfulAsset) {
                    /** @var ContentfulAsset $entry */
                    $fields['cfMeta']['itemName']    = $fields['title'];
                    $fields['cfMeta']['contentType'] = 'Asset';

                }
                return $this->createView($fields, $spaceId, $locale);
            } else {
                $newValue = array();
                foreach ($value as $k => $v) {
                    $newValue[$k] = $this->getValue($v, $spaceId, $locale);
                }
                return $newValue;
            }
        }
    }

    protected function createView(array $fields, $spaceId, $locale)
    {
        $view = new \stdClass();
        foreach ($fields as $k => $v) {
            $view->$k = $this->getValue($v, $spaceId, $locale);
        }
        $view = $this->dispatcher->dispatch(BaseWebsiteBundleEvents::CONTENTFUL_VIEW_CREATE, new ContentfulViewEvent($view))->getView();
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
