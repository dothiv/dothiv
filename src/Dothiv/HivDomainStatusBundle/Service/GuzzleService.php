<?php


namespace Dothiv\HivDomainStatusBundle\Service;

use Dothiv\APIBundle\JsonLd\Client\JsonLdGuzzleClient;
use Dothiv\APIBundle\JsonLd\JsonLdTypedEntityInterface;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\HivDomainStatusBundle\Event\HivDomainStatusEvent;
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
        $domainListLink = $this->findDomainListLink();
        $this->client->post($domainListLink->getJsonLdId(), array('name' => $domain->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterDomain(Domain $domain)
    {
        $domainListLink = $this->findDomainListLink();
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
    public function fetchDomains()
    {
        $domainListLink = $this->findDomainListLink();
        $url            = Option::fromValue($domainListLink->getJsonLdId());
        do {
            $list    = $this->client->getListResponse($url->get(), '\Dothiv\HivDomainStatusBundle\Model\DomainModel');
            $nextUrl = Option::fromValue($list->getNextPageUrl());
            if ($nextUrl->isDefined() && !$nextUrl->get()->equals($url->get())) {
                $url = $nextUrl;
            } else {
                $url = None::create();
            }
            // Fetch check
            if ($list->count() > 0) {
                foreach ($list->getItems() as $item) {
                    /** @var DomainModel $item */
                    $this->dispatcher->dispatch(HivDomainStatusEvents::DOMAIN_FETCHED, new HivDomainStatusEvent($item));
                }
            }
        } while ($url->isDefined());
    }

    /**
     * @return JsonLdTypedEntityInterface
     */
    protected function findDomainListLink()
    {
        $entryPoint = $this->client->getEntryPointResponse($this->endpoint);
        // Find Domain list
        $domainListLinkOptional = $entryPoint->getLink(new URLValue('http://jsonld.click4life.hiv/List'), new URLValue('http://jsonld.click4life.hiv/Domain'));
        if ($domainListLinkOptional->isEmpty()) {
            throw new ServiceException(sprintf('Endpoint has no Domain list link: %s', $this->endpoint));
        }
        /** @var JsonLdTypedEntityInterface $domainListLink */
        $domainListLink = $domainListLinkOptional->get();
        return $domainListLink;
    }
}
