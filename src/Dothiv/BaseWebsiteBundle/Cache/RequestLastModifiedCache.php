<?php

namespace Dothiv\BaseWebsiteBundle\Cache;

use Doctrine\Common\Cache\Cache;
use Dothiv\BaseWebsiteBundle\Event\ContentfulViewEvent;
use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use PhpOption\None;
use PhpOption\Option;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Stores modification times for requests based on the update date of content items rendered on the page.
 */
class RequestLastModifiedCache
{
    use LoggerAwareTrait;

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
        $optionalLastModified = Option::fromValue($this->cache->fetch($this->getCacheKeyRequest(sha1($request->getUri()), 'lastmodified')), false);
        if ($optionalLastModified->isEmpty()) {
            return None::create();
        } else {
            return Option::fromValue(new \DateTime($optionalLastModified->get()));
        }
    }

    /**
     * Stores the last modified date for a request.
     *
     * @param Request   $request
     * @param \DateTime $lastModified
     */
    public function setLastModified(Request $request, \DateTime $lastModified)
    {
        $this->cache->save($this->getCacheKeyRequest(sha1($request->getUri()), 'lastmodified'), $lastModified->format('r'));

        foreach ($this->itemIds as $itemId => $bool) {
            $key                             = $this->getCacheKeyItem($itemId, 'uri');
            $urisForItem                     = Option::fromValue($this->cache->fetch($key), false)->getOrElse(array());
            $urisForItem[$request->getUri()] = $bool;
            $this->cache->save($key, $urisForItem);
            Option::fromValue($this->logger)->map(function (LoggerInterface $logger) use ($request, $itemId) {
                $logger->debug(sprintf('[Dothiv:RequestLastModifiedCache] "%s" is used on "%s".', $itemId, $request->getUri()));
            });
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
            Option::fromValue($this->logger)->map(function (LoggerInterface $logger) use ($entry) {
                $logger->debug(sprintf('[Dothiv:RequestLastModifiedCache] Entry "%s" is not used.', $entry->getId()));
            });
            return;
        }
        // Update
        $urisForItem = $urisForItemOption->get();
        foreach ($urisForItem as $uri => $bool) {
            $key = $this->getCacheKeyRequest(sha1($uri), 'lastmodified');
            $lastModified = $this->cache->fetch($key);
            if ($lastModified >= $entry->getUpdatedAt()->format('r')) {
                Option::fromValue($this->logger)->map(function (LoggerInterface $logger) use ($lastModified, $uri) {
                    $logger->debug(sprintf('[Dothiv:RequestLastModifiedCache] "%s" was last modified at "%s" to "%s". Entry is older.', $uri, $lastModified));
                });
                continue;
            }
            $this->cache->save($key, $entry->getUpdatedAt()->format('r'));
            Option::fromValue($this->logger)->map(function (LoggerInterface $logger) use ($entry, $uri) {
                $logger->debug(sprintf('[Dothiv:RequestLastModifiedCache] Setting last modified time for "%s" to "%s".', $uri, $entry->getUpdatedAt()->format('r')));
            });
        }
    }
} 
