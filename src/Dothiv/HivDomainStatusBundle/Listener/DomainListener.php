<?php

namespace Dothiv\HivDomainStatusBundle\Listener;

use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\HivDomainStatusBundle\Service\HivDomainStatusServiceInterface;

/**
 * This listeners registers and unregisters domain with the hiv domain status service.
 */
class DomainListener
{
    /**
     * @var HivDomainStatusServiceInterface
     */
    private $service;

    /**
     * @param HivDomainStatusServiceInterface $service
     */
    public function __construct(HivDomainStatusServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param DomainEvent $event
     */
    public function onDomainRegistered(DomainEvent $event)
    {
        $this->service->registerDomain($event->getDomain());
    }

    /**
     * @param DomainEvent $event
     */
    public function onDomainDeleted(DomainEvent $event)
    {
        $this->service->unregisterDomain($event->getDomain());
    }
}
