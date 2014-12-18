<?php

namespace Dothiv\ShopBundle\Listener;

use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\ShopBundle\Repository\DomainInfoRepositoryInterface;
use Dothiv\ValueObject\HivDomainValue;

/**
 * This listeners registers and unregisters domain with the hiv domain status service.
 */
class DomainListener
{
    /**
     * @var DomainInfoRepositoryInterface
     */
    private $domainInfoRepo;

    /**
     * @param DomainInfoRepositoryInterface $domainInfoRepo
     */
    public function __construct(DomainInfoRepositoryInterface $domainInfoRepo)
    {
        $this->domainInfoRepo = $domainInfoRepo;
    }

    /**
     * @param DomainEvent $event
     */
    public function onDomainRegistered(DomainEvent $event)
    {
        $info = $this->domainInfoRepo->getByDomain(new HivDomainValue($event->getDomain()->getName()));
        $info->setRegistered(true);
        $this->domainInfoRepo->persist($info)->flush();
    }

    /**
     * @param DomainEvent $event
     */
    public function onDomainDeleted(DomainEvent $event)
    {
        $info = $this->domainInfoRepo->getByDomain(new HivDomainValue($event->getDomain()->getName()));
        $info->setRegistered(false);
        $this->domainInfoRepo->persist($info)->flush();
    }
}
