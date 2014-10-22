<?php


namespace Dothiv\AfiliasImporterBundle\Service;

use Dothiv\AfiliasImporterBundle\AfiliasImporterBundleEvents;
use Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent;
use Dothiv\AfiliasImporterBundle\Exception\ServiceException;
use Dothiv\AfiliasImporterBundle\Model\PaginatedList;
use Dothiv\ContentfulBundle\Exception\RuntimeException;
use Dothiv\ValueObject\URLValue;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Response;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AfiliasImporterService implements AfiliasImporterServiceInterface
{

    /**
     * @var \Guzzle\Http\ClientInterface
     */
    private $client;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ClientInterface          $client
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ClientInterface $client, EventDispatcherInterface $eventDispatcher)
    {
        $this->client          = $client;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRegistrations(URLValue $url)
    {
        $request  = $this->client->get((string)$url, array('Accept' => 'application/json'));
        $response = $request->send();
        $list     = $this->getListResponse($response, '\Dothiv\AfiliasImporterBundle\Event\DomainRegisteredEvent');

        foreach ($list->getItems() as $event) {
            $this->eventDispatcher->dispatch(AfiliasImporterBundleEvents::DOMAIN_REGISTERED, $event);
        }

        return $list->getNextUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchTransactions(URLValue $url)
    {
        $request  = $this->client->get((string)$url, array('Accept' => 'application/json'));
        $response = $request->send();
        $list     = $this->getListResponse($response, '\Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent');

        foreach ($list->getItems() as $event) {
            /** @var DomainTransactionEvent $event */
            $type = null;
            switch ($event->Command) {
                case 'CREATE':
                    $type = AfiliasImporterBundleEvents::DOMAIN_CREATED;
                    break;
                case 'UPDATE':
                    $type = AfiliasImporterBundleEvents::DOMAIN_UPDATED;
                    break;
                case 'DELETE':
                    $type = AfiliasImporterBundleEvents::DOMAIN_DELETED;
                    break;
                case 'TRANSFER':
                    $type = AfiliasImporterBundleEvents::DOMAIN_TRANSFERRED;
                    break;
            }
            if (Option::fromValue($type)->isEmpty()) {
                throw new ServiceException(
                    sprintf(
                        'Unexpected Command for DomainTransactionEvent: "%s"!',
                        $event->Command
                    )
                );
            }
            $this->eventDispatcher->dispatch($type, $event);
        }

        return $list->getNextUrl();
    }

    protected function getListResponse(Response $response, $itemClass)
    {
        if ($response->getContentType() != 'application/json') {
            throw new ServiceException(
                sprintf(
                    'Unexpected contenttype "%s", expected "application/json"', $response->getContentType()
                )
            );
        }
        $data = json_decode($response->getBody());
        if (!is_object($data)) {
            throw new ServiceException(
                sprintf(
                    'Unexpected %s, expected object', gettype($data)
                )
            );
        }

        $this->expectProperty($data, '@context', 'http://jsonld.click4life.hiv/List');
        $context = $id = constant("$itemClass::CONTEXT");
        $this->expectProperty($data, '@type', $context);
        $list = new PaginatedList();
        $list->setTotal($data->total);
        foreach ($data->items as $item) {
            $instance = new $itemClass();
            foreach (get_class_vars($itemClass) as $k => $v) {
                $instance->$k = $item->$k;

            }
            $list->add($instance);
        }

        $list->setNextUrl($this->getNextUrl($response));
        return $list;
    }

    /**
     * @param \stdClass $object
     * @param string    $property
     * @param string    $expectedValue
     *
     * @return mixed
     * @throws \Dothiv\AfiliasImporterBundle\Exception\ServiceException
     */
    protected function expectProperty(\stdClass $object, $property, $expectedValue)
    {
        if (!property_exists($object, $property)) {
            throw new ServiceException(
                sprintf(
                    'Missing %s in "%s"', $property, substr(json_encode($object), 0, 25)
                )
            );
        }
        $value = $object->{$property};
        if ($value != $expectedValue) {
            throw new ServiceException(
                sprintf(
                    'Invalid %s "%s", expected "%s" in "%s"', $property, $value, $expectedValue, substr(json_encode($object), 0, 25)
                )
            );
        }
        return $value;
    }

    /**
     * @param Response $response
     *
     * @return URLValue|null
     */
    protected function getNextUrl(Response $response)
    {
        $linkHeader = $response->getHeader('Link');
        if (empty($linkHeader)) {
            return null;
        }
        if (preg_match('/<([^>]+)>; *rel="next"/', $linkHeader, $nextMatch)) {
            $path  = $nextMatch[1];
            $parts = parse_url($response->getEffectiveUrl());
            return new URLValue(sprintf('%s://%s:%d%s', $parts['scheme'], $parts['host'], $parts['port'], $path));
        }
    }
}
