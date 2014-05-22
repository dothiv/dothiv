<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Client\HttpClient;
use Dothiv\ContentfulBundle\ContentfulEvents;
use Dothiv\ContentfulBundle\Event\ContentfulAssetEvent;
use Dothiv\ContentfulBundle\Event\ContentfulContentTypeEvent;
use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use Dothiv\ContentfulBundle\Repository\ContentfulContentTypeRepository;
use PhpOption\Option;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HttpClientAdapter implements ContentfulApiAdapter
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $nextSyncUrl;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $spaceId;

    /**
     * @param string                   $spaceId
     * @param string                   $accessToken
     * @param HttpClient               $client
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($spaceId, $accessToken, HttpClient $client, EventDispatcherInterface $dispatcher)
    {
        $this->spaceId = $spaceId;
        $this->baseUrl     = sprintf(
            'https://cdn.contentful.com/spaces/%s/',
            urlencode($spaceId)
        );
        $this->accessToken = $accessToken;
        $this->client      = $client;
        $this->dispatcher  = $dispatcher;
    }

    /**
     * @param \stdClass       $data
     * @param ArrayCollection $contentTypes
     *
     * @return ContentfulAsset|ContentfulEntry|null
     */
    protected function getEntry(\stdClass $data, ArrayCollection $contentTypes)
    {
        $postFill = function () {
        };
        switch ($data->sys->type) {
            case 'Entry':
                /** @var ContentfulContentType $contentType */
                $contentType = $contentTypes->get($data->sys->contentType->sys->id);
                $entry = new ContentfulEntry();
                $entry->setContentTypeId($contentType->getId());
                $postFill = function () use ($contentType, $entry) {
                    $contentType->updateEntryName($entry);
                };
                break;
            case 'Asset':
                $entry = new ContentfulAsset();
                break;
            default:
                return;
        }

        $entry->setId($data->sys->id);
        $entry->setRevision($data->sys->revision);
        $entry->setSpaceId($this->spaceId);
        $entry->setCreatedAt(new \DateTime($data->sys->createdAt));
        $entry->setUpdatedAt(new \DateTime($data->sys->updatedAt));

        foreach ($data->fields as $k => $field) {
            if (is_array($field)) {
                $fieldValue = array();
                foreach ($field as $subItem) {
                    $fieldValue[] = $this->getEntry($subItem, $contentTypes);
                }
                $entry->$k = $fieldValue;
            } else if (is_object($field) && property_exists($field, 'sys')) {
                $entry->$k = $this->getEntry($field, $contentTypes);
            } else {
                $entry->$k = $field;
            }
        }

        $postFill();

        return $entry;
    }

    /**
     * @return string The next sync URL.
     */
    public function sync()
    {
        $this->log('Syncing from %s ...', str_replace($this->accessToken, '*****', $this->getNextSyncUrl()));
        $types = $this->syncContentTypes();
        $this->syncFrom($this->getNextSyncUrl(), $types);
        return $this->nextSyncUrl;
    }

    /**
     * @return ContentfulContentType[]|ArrayCollection
     */
    protected function syncContentTypes()
    {
        $response = $this->client->get($this->buildUrl('content_types'));
        $data     = json_decode($response);
        $types    = new ArrayCollection();
        foreach ($data->items as $ctype) {
            $contentType = new ContentfulContentType();
            $contentType->setName($ctype->name);
            $contentType->setDisplayField($ctype->displayField);
            $contentType->setId($ctype->sys->id);
            $contentType->setRevision($ctype->sys->revision);
            $contentType->setSpaceId($this->spaceId);
            $contentType->setCreatedAt(new \DateTime($ctype->sys->createdAt));
            $contentType->setUpdatedAt(new \DateTime($ctype->sys->updatedAt));
            $this->log('Sync: %s', $contentType);
            /** @var ContentfulContentTypeEvent $event */
            $event                        = $this->dispatcher->dispatch(
                ContentfulEvents::CONTENT_TYPE_SYNC,
                new ContentfulContentTypeEvent($contentType)
            );
            $types[$contentType->getId()] = $event->getContentType();
        }
        return $types;
    }

    protected function syncFrom($url, ArrayCollection $contentTypes)
    {
        $response = $this->client->get($url);
        $data     = json_decode($response);
        $this->log('Fetched %d items.', count($data->items));
        foreach ($data->items as $item) {
            $entry = $this->getEntry($item, $contentTypes);
            if ($entry) {
                $this->log('Sync: %s', $entry);
                if ($entry instanceof ContentfulAsset) {
                    $this->dispatcher->dispatch(ContentfulEvents::ASSET_SYNC, new ContentfulAssetEvent($entry));
                } else {
                    $this->dispatcher->dispatch(ContentfulEvents::ENTRY_SYNC, new ContentfulEntryEvent($entry));
                }
            }
        }
        if (property_exists($data, 'nextPageUrl')) {
            $this->log('Continuing with next page.');
            $this->syncFrom($data->nextPageUrl, $contentTypes);
        }
        if (property_exists($data, 'nextSyncUrl')) {
            // FIXME: store next sync URL
            $this->nextSyncUrl = $data->nextSyncUrl;
            $this->log('Done. Start next sync from %s', $data->nextSyncUrl);
        }
    }

    /**
     * @return string
     */
    protected function getNextSyncUrl()
    {
        return Option::fromValue($this->nextSyncUrl)->getOrElse($this->buildUrl('sync', array('initial' => 'true')));
    }

    public function setNextSyncUrl($nextSyncUrl)
    {
        $this->nextSyncUrl = $nextSyncUrl . '&access_token=' . $this->accessToken;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     *
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function log()
    {
        $args = func_get_args();
        Option::fromValue($this->logger)->map(function (LoggerInterface $logger) use ($args) {
            $logger->debug(call_user_func_array('sprintf', $args));
        });
    }

    /**
     * @param string $path
     * @param array  $params
     *
     * @return string
     */
    protected function buildUrl($path, array $params = null)
    {
        if ($params == null) {
            $params = array();
        }
        $params['access_token'] = $this->accessToken;
        $url                    = $this->baseUrl . $path . '?' . http_build_query($params);
        return $url;
    }
}
