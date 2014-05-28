<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Client\HttpClientInterface;
use Dothiv\ContentfulBundle\ContentfulEvents;
use Dothiv\ContentfulBundle\Event\ContentfulAssetEvent;
use Dothiv\ContentfulBundle\Event\ContentfulContentTypeEvent;
use Dothiv\ContentfulBundle\Event\ContentfulContentTypesEvent;
use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use Dothiv\ContentfulBundle\Event\DeletedContentfulEntryEvent;
use Dothiv\ContentfulBundle\Exception\RuntimeException;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use Dothiv\ContentfulBundle\Item\DeletedContentfulEntry;
use Dothiv\ContentfulBundle\Logger\LoggerAwareTrait;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HttpClientAdapter implements ContentfulApiAdapter
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var HttpClientInterface
     */
    private $client;

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
     * @param HttpClientInterface      $client
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($spaceId, HttpClientInterface $client, EventDispatcherInterface $dispatcher)
    {
        $this->spaceId    = $spaceId;
        $this->baseUrl    = sprintf(
            'https://cdn.contentful.com/spaces/%s/',
            urlencode($spaceId)
        );
        $this->client     = $client;
        $this->dispatcher = $dispatcher;
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
                $entry       = new ContentfulEntry();
                $entry->setContentTypeId($contentType->getId());
                $postFill = function () use ($contentType, $entry) {
                    $contentType->updateEntryName($entry);
                };
                break;
            case 'Asset':
                $entry = new ContentfulAsset();
                break;
            case 'DeletedEntry':
                $entry = new DeletedContentfulEntry();
                break;
            default:
                return;
        }

        $entry->setId($data->sys->id);
        $entry->setRevision($data->sys->revision);
        $entry->setSpaceId($this->spaceId);
        $entry->setCreatedAt(new \DateTime($data->sys->createdAt));
        $entry->setUpdatedAt(new \DateTime($data->sys->updatedAt));

        if (property_exists($data, 'fields')) {
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
        }

        $postFill();

        return $entry;
    }

    /**
     * @return string The next sync URL.
     */
    public function sync()
    {
        $this->log('Syncing from %s ...', $this->getNextSyncUrl());
        $types = $this->syncContentTypes();
        $this->syncFrom($this->getNextSyncUrl(), $types);
        return $this->nextSyncUrl;
    }

    /**
     * @return ContentfulContentType[]|ArrayCollection
     */
    protected function syncContentTypes()
    {
        $data  = $this->fetch($this->buildUrl('content_types'));
        $types = new ArrayCollection();
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
        $this->dispatcher->dispatch(
            ContentfulEvents::CONTENT_TYPE_SYNC_ALL,
            new ContentfulContentTypesEvent($types)
        );
        return $types;
    }

    protected function syncFrom($url, ArrayCollection $contentTypes)
    {
        $data = $this->fetch($url);
        foreach ($data->items as $item) {
            $entry = $this->getEntry($item, $contentTypes);
            if ($entry) {
                if ($entry instanceof DeletedContentfulEntry) {
                    $this->log('Delete: %s', $entry);
                    $this->dispatcher->dispatch(ContentfulEvents::ENTRY_DELETE, new DeletedContentfulEntryEvent($entry));
                } elseif ($entry instanceof ContentfulAsset) {
                    $this->log('Sync: %s', $entry);
                    $this->dispatcher->dispatch(ContentfulEvents::ASSET_SYNC, new ContentfulAssetEvent($entry));
                } else {
                    $this->log('Sync: %s', $entry);
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
        $this->nextSyncUrl = $nextSyncUrl;
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
        $url = $this->baseUrl . $path . '?' . http_build_query($params);
        return $url;
    }

    /**
     * @param string $url
     *
     * @return object
     * @throws \Dothiv\ContentfulBundle\Exception\RuntimeException
     */
    protected function fetch($url)
    {
        $response = $this->client->get($url);
        $data     = json_decode($response);
        if (!is_object($data) || !property_exists($data, 'items')) {
            throw new RuntimeException(
                sprintf(
                    'Missing items in response for "%s"',
                    $url
                )
            );
        }
        $this->log('Fetched %d items.', count($data->items));
        return $data;
    }
}
