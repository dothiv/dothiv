<?php

namespace Dothiv\BaseWebsiteBundle\Cache;

use Doctrine\Common\Cache\Cache;
use Dothiv\BaseWebsiteBundle\BaseWebsiteBundleEvents;
use Dothiv\BaseWebsiteBundle\Event\ContentfulViewEvent;
use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * Stores modification times for requests based on the update date of content items rendered on the page.
 */
class RequestLastModifiedCache
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var \DateTime
     */
    private $lastModifiedContent;

    /**
     * @var array
     */
    private $itemIds = array();

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Collect ViewEvents to build lastModified date.
     *
     * @param ContentfulViewEvent $e
     */
    public function onViewCreate(ContentfulViewEvent $e)
    {
        $viewMeta                           = $e->getView()->cfMeta;
        $updated                            = $viewMeta['updatedAt'];
        $this->itemIds[$viewMeta['itemId']] = true;
        if ($this->lastModifiedContent === null) {
            $this->lastModifiedContent = $updated;
        } else {
            if ($this->lastModifiedContent < $updated) {
                $this->lastModifiedContent = $updated;
            }
        }
    }

    /**
     * Returns the last modified date for a request.
     *
     * @param Request $request
     *
     * @return Option
     */
    public function getLastModified(Request $request)
    {
        return Option::fromValue($this->cache->fetch($this->getCacheKeyRequest(sha1($request->getUri()), 'lastmodified')), false);
    }

    /**
     * Stores the last modified date for a request.
     *
     * @param Request   $request
     * @param \DateTime $lastModified
     */
    public function setLastModified(Request $request, \DateTime $lastModified)
    {
        $this->cache->save($this->getCacheKeyRequest(sha1($request->getUri()), 'lastmodified'), $lastModified);

        foreach ($this->itemIds as $itemId => $bool) {
            $key                                   = $this->getCacheKeyItem($itemId, 'uri');
            $urisForItem                           = Option::fromValue($this->cache->fetch($key), false)->getOrElse(array());
            $urisForItem[sha1($request->getUri())] = $bool;
            $this->cache->save($key, $urisForItem);
        }
    }

    /**
     * @param string $uri
     * @param string $type
     *
     * @return string
     */
    protected function getCacheKeyRequest($uri, $type)
    {
        $cacheKey = 'dothiv_base_website-request_uri-' . $type . '-' . $uri;
        return $cacheKey;
    }

    /**
     * @param string $itemId
     * @param string $type
     *
     * @return string
     */
    protected function getCacheKeyItem($itemId, $type)
    {
        $cacheKey = 'dothiv_base_website-item_uri-' . $itemId . '-' . $type;
        return $cacheKey;
    }

    /**
     * @return \DateTime
     */
    public function getLastModifiedContent()
    {
        return $this->lastModifiedContent;
    }

    public function onEntryUpdate(ContentfulEntryEvent $e)
    {
        $entry             = $e->getEntry();
        $key               = $this->getCacheKeyItem($entry->getId(), 'uri');
        $urisForItemOption = Option::fromValue($this->cache->fetch($key), false);
        if ($urisForItemOption->isEmpty()) {
            return;
        }
        // Update
        $urisForItem = $urisForItemOption->get();
        foreach ($urisForItem as $uri => $bool) {
            $key          = $this->getCacheKeyRequest($uri, 'lastmodified');
            $lastModified = $this->cache->fetch($key);
            if ($lastModified >= $entry->getUpdatedAt()) {
                continue;
            }
            $this->cache->save($key, $entry->getUpdatedAt());
        }
    }
} 
