<?php

namespace Dothiv\BaseWebsiteBundle\Cache;

use Doctrine\Common\Cache\Cache;
use Dothiv\BaseWebsiteBundle\BaseWebsiteBundleEvents;
use Dothiv\BaseWebsiteBundle\Event\ContentfulViewEvent;
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
        $this->itemIds[$viewMeta['itemId']] = $updated;
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
        return Option::fromValue($this->cache->fetch($this->getCacheKey($request, 'lastmodified')), false);
    }

    /**
     * Stores the last modified date for a request.
     *
     * @param Request   $request
     * @param \DateTime $lastModified
     */
    public function setLastModified(Request $request, \DateTime $lastModified)
    {
        $this->cache->save($this->getCacheKey($request, 'lastmodified'), $lastModified);
    }

    /**
     * @param Request $request
     * @param string  $type
     *
     * @return string
     */
    protected function getCacheKey(Request $request, $type)
    {
        $cacheKey = 'dothiv_base_website-request_uri-' . $type . '-' . sha1($request->getUri());
        return $cacheKey;
    }

    /**
     * @return \DateTime
     */
    public function getLastModifiedContent()
    {
        return $this->lastModifiedContent;
    }
} 
