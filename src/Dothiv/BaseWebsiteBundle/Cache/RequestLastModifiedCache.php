<?php

namespace Dothiv\BaseWebsiteBundle\Cache;

use Doctrine\Common\Cache\Cache;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\Request;

/**
 * Stores modification times for requests.
 */
class RequestLastModifiedCache
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
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
        return Option::fromValue($this->cache->fetch($this->getCacheKey($request)), false);
    }

    /**
     * Stores the last modified date for a request.
     *
     * @param Request   $request
     * @param \DateTime $lastModified
     */
    public function setLastModified(Request $request, \DateTime $lastModified)
    {
        $this->cache->save($this->getCacheKey($request), $lastModified);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function getCacheKey(Request $request)
    {
        $cacheKey = 'dothiv_base_website-request_uri-lastmodified-' . sha1($request->getUri());
        return $cacheKey;
    }
} 
