<?php


namespace Dothiv\AfiliasImporterBundle\Service;

use Dothiv\AfiliasImporterBundle\AfiliasImporterBundleEvents;
use Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent;
use Dothiv\AfiliasImporterBundle\Exception\ServiceException;
use Dothiv\AfiliasImporterBundle\Model\PaginatedList;
use Dothiv\APIBundle\JsonLd\Client\JsonLdGuzzleClient;
use Dothiv\ValueObject\URLValue;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AfiliasImporterService implements AfiliasImporterServiceInterface
{

    /**
     * @var JsonLdGuzzleClient
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
        $this->client          = new JsonLdGuzzleClient($client);
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRegistrations(URLValue $url)
    {
        $list = $this->client->getListResponse($url, '\Dothiv\AfiliasImporterBundle\Event\DomainRegisteredEvent');

        if ($list->count() > 0) {
            foreach ($list->getItems() as $event) {
                $this->eventDispatcher->dispatch(AfiliasImporterBundleEvents::DOMAIN_REGISTERED, $event);
            }
        }

        return $list->getNextPageUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchTransactions(URLValue $url)
    {
        $list = $this->client->getListResponse($url, '\Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent');

        foreach ($list->getItems() as $event) {
            /** @var DomainTransactionEvent $event */
            switch ($event->ObjectType) {
                case 'DOMAIN':
                    continue; // pass
                case 'NAMESERVER':
                    continue 2; // not interesting
                default:
                    throw new ServiceException(
                        sprintf(
                            'Unexpected ObjectType for DomainTransactionEvent: "%s"!',
                            $event->ObjectType
                        )
                    );
            }
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
                case 'RENEW':
                    $type = AfiliasImporterBundleEvents::DOMAIN_RENEWED;
                    break;
                default:
                    throw new ServiceException(
                        sprintf(
                            'Unexpected Command for DomainTransactionEvent: "%s"!',
                            $event->Command
                        )
                    );
            }
            $this->eventDispatcher->dispatch($type, $event);
        }

        return $list->getNextPageUrl();
    }
}
