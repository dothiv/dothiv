<?php


namespace Dothiv\HivDomainStatusBundle\Service;

use Dothiv\APIBundle\JsonLd\Client\JsonLdGuzzleClient;
use Dothiv\APIBundle\JsonLd\JsonLdTypedEntityInterface;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\HivDomainStatusBundle\Event\DomainCheckEvent;
use Dothiv\HivDomainStatusBundle\Exception\ServiceException;
use Dothiv\HivDomainStatusBundle\HivDomainStatusEvents;
use Dothiv\HivDomainStatusBundle\Model\DomainModel;
use Dothiv\ValueObject\URLValue;
use Guzzle\Http\Client;
use PhpOption\None;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GuzzleService implements HivDomainStatusServiceInterface
{

    /**
     * @var JsonLdGuzzleClient
     */
    private $client;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var URLValue
     */
    private $endpoint;

    /**
     * @param Client                   $client
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $endpoint
     */
    public function __construct(Client $client, EventDispatcherInterface $dispatcher, $endpoint)
    {
        $this->client     = new JsonLdGuzzleClient($client);
        $this->dispatcher = $dispatcher;
        $this->endpoint   = new URLValue($endpoint);
    }

    /**
     * {@inheritdoc}
     */
    public function registerDomain(Domain $domain)
    {
        $domainListLink = $this->findListLink(new URLValue('http://jsonld.click4life.hiv/Domain'));
        $this->client->post($domainListLink->getJsonLdId(), array('name' => $domain->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterDomain(Domain $domain)
    {
        $domainListLink = $this->findListLink(new URLValue('http://jsonld.click4life.hiv/Domain'));
        $domainFound    = false;
        $url            = Option::fromValue($domainListLink->getJsonLdId());
        do {
            $list = $this->client->getListResponse($url->get(), '\Dothiv\HivDomainStatusBundle\Model\DomainModel');
            $url  = Option::fromValue($list->getNextPageUrl());
            foreach ($list->getItems() as $item) {
                /** @var DomainModel $item */
                if ($item->name === $domain->getName()) {
                    $this->client->delete($item->getJsonLdId());
                    return;
                }
            }
        } while (!$domainFound && $url->isDefined());
    }

    /**
     * {@inheritdoc}
     */
    public function fetchChecks(URLValue $url = null)
    {
        if ($url === null) {
            $checkListLink = $this->findListLink(new URLValue('http://jsonld.click4life.hiv/DomainCheck'));
            $url           = $checkListLink->getJsonLdId();
        }
        $list    = $this->client->getListResponse($url, '\Dothiv\HivDomainStatusBundle\Model\DomainCheckModel');
        $nextUrl = $list->getNextPageUrl();
        // Fetch check
        if ($list->count() > 0) {
            foreach ($list->getItems() as $item) {
                /** @var DomainModel $item */
                $this->dispatcher->dispatch(HivDomainStatusEvents::DOMAIN_CHECK, new DomainCheckEvent($item));
            }
        }
        return $nextUrl;
    }

    /**
     * @param URLValue $type
     *
     * @return JsonLdTypedEntityInterface
     */
    protected function findListLink(URLValue $type)
    {
        $entryPoint = $this->client->getEntryPointResponse($this->endpoint);
        // Find Domain list
        $domainListLinkOptional = $entryPoint->getLink(new URLValue('http://jsonld.click4life.hiv/List'), $type);
        if ($domainListLinkOptional->isEmpty()) {
            throw new ServiceException(sprintf('Endpoint has no Domain list link: %s', $this->endpoint));
        }
        /** @var JsonLdTypedEntityInterface $domainListLink */
        $domainListLink = $domainListLinkOptional->get();
        return $domainListLink;
    }
}
