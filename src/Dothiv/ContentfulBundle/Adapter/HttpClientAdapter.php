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
use Dothiv\ContentfulBundle\Exception\InvalidArgumentException;
use Dothiv\ContentfulBundle\Exception\RuntimeException;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\DeletedContentfulEntry;
use Dothiv\ContentfulBundle\Logger\LoggerAwareTrait;
use Dothiv\ContentfulBundle\Adapter\ContentfulEntityReader;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HttpClientAdapter implements ContentfulApiAdapter
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $endpoint = 'https://cdn.contentful.com';

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
        $this->client     = $client;
        $this->dispatcher = $dispatcher;
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
        $data   = $this->fetch($this->buildUrl('content_types'));
        $types  = new ArrayCollection();
        $reader = new ContentfulContentTypeReader($this->spaceId);
        foreach ($data->items as $ctype) {
            $contentType = $reader->getContentType($ctype);
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
        $reader = new ContentfulEntityReader($this->spaceId, $contentTypes);
        $data   = $this->fetch($url);
        foreach ($data->items as $item) {
            $entry = $reader->getEntry($item);
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
            // FIXME: store next sync URL.
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
        $url = sprintf(
            '%s/spaces/%s/%s?%s',
            $this->endpoint,
            urlencode($this->spaceId),
            $path,
            http_build_query($params)
        );
        return $url;
    }

    /**
     * @param string $url
     *
     * @return object
     * @throws RuntimeException
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

    /**
     * @param string $endpoint
     *
     * @throws InvalidArgumentException
     */
    public function setEndpoint($endpoint)
    {
        if (!filter_var($endpoint, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Not an url: %s',
                    $endpoint
                )
            );
        }
        $parts          = parse_url($endpoint);
        $this->endpoint = sprintf('%s://%s', $parts['scheme'], $parts['host']);
    }
}
