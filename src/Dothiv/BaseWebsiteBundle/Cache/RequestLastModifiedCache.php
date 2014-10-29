<?php

namespace Dothiv\BaseWebsiteBundle\Cache;

use Doctrine\Common\Cache\Cache;
use Dothiv\BaseWebsiteBundle\Event\ContentfulViewEvent;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use PhpOption\None;
use PhpOption\Option;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Stores modification times for requests based on the
 * update date of content items rendered on the page.
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
     * @var Config
     */
    private $lastMinModified;

    /**
     * @var \DateTime
     */
    private $lastMinModifiedDate;

    /**
     * @var array
     */
    private $itemIds = array();

    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepo;

    /**
     * @param Cache                     $cache
     * @param ConfigRepositoryInterface $configRepo
     */
    public function __construct(Cache $cache, ConfigRepositoryInterface $configRepo)
    {
        $this->cache      = $cache;
        $this->configRepo = $configRepo;
    }

    /**
     * Collect ViewEvents to build lastModified date.
     *
     * @param ContentfulViewEvent $e
     */
    public function onViewCreate(ContentfulViewEvent $e)
    {
        $viewMeta = $e->getView()->cfMeta;
        if ($viewMeta['contentType'] == 'String') {
            return;
        }
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
        $optionalMinModified  = $this->getLastMinModifiedDate();
        $optionalLastModified = Option::fromValue($this->cache->fetch($this->getCacheKeyRequest(sha1($request->getUri()), 'lastmodified')), false);
        if ($optionalMinModified->orElse($optionalLastModified)->isEmpty()) {
            return None::create();
        }
        if ($optionalLastModified->isEmpty()) {
            return $optionalMinModified;
        } else if ($optionalMinModified->isEmpty()) {
            return Option::fromValue(new \DateTime($optionalLastModified->get()));
        }
        return Option::fromValue(max($optionalMinModified->get(), new \DateTime($optionalLastModified->get())));
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
        $lastMinModifiedDateOptional = $this->getLastMinModifiedDate();
        if ($lastMinModifiedDateOptional->isDefined()) {
            return max($this->lastModifiedContent, $lastMinModifiedDateOptional->get());
        }
        return $this->lastModifiedContent;
    }

    /**
     * This method gets called when an Entry is update and updates the
     * last modified time cache entry for every page it is used.
     *
     * There is a problem with this implementation: adding _new_ child elements
     * is not picked up because they were never used before.
     *
     * FIXME: find parent elements and trigger an update there, too.
     *
     * @param ContentfulEntryEvent $e
     */
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
            $key          = $this->getCacheKeyRequest(sha1($uri), 'lastmodified');
            $lastModified = $this->cache->fetch($key);
            if ($lastModified >= $entry->getUpdatedAt()->format('r')) {
                Option::fromValue($this->logger)->map(function (LoggerInterface $logger) use ($lastModified, $uri) {
                    $logger->debug(sprintf('[Dothiv:RequestLastModifiedCache] "%s" was last modified at "%s". Entry is older.', $uri, $lastModified));
                });
                continue;
            }
            $this->cache->save($key, $entry->getUpdatedAt()->format('r'));
            Option::fromValue($this->logger)->map(function (LoggerInterface $logger) use ($entry, $uri) {
                $logger->debug(sprintf('[Dothiv:RequestLastModifiedCache] Setting last modified time for "%s" to "%s".', $uri, $entry->getUpdatedAt()->format('r')));
            });
        }
    }

    /**
     * @return Option of \DateTime
     */
    protected function getLastMinModifiedDate()
    {
        if ($this->lastMinModified === null) {
            $this->lastMinModified = $this->configRepo->get('last_modified_content.min_last_modified');
            $v = $this->lastMinModified->getValue();
            if (!empty($v)) {
                $this->lastMinModifiedDate = new \DateTime($v);
            }
        }
        return Option::fromValue($this->lastMinModifiedDate);
    }
} 
