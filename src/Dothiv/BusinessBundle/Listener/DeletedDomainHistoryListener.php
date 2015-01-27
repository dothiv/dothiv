<?php

namespace Dothiv\BusinessBundle\Listener;

use Dothiv\BusinessBundle\Entity\DeletedDomain;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\BusinessBundle\Repository\DeletedDomainRepositoryInterface;
use Dothiv\ValueObject\HivDomainValue;

/**
 * This listener ads a history entry if a domains gets deleted
 */
class DeletedDomainHistoryListener
{

    /**
     * @var DeletedDomainRepositoryInterface
     */
    private $deletedDomainRepo;

    /**
     * @param DeletedDomainRepositoryInterface $deletedDomainRepo
     */
    public function __construct(
        DeletedDomainRepositoryInterface $deletedDomainRepo
    )
    {
        $this->deletedDomainRepo = $deletedDomainRepo;
    }

    /**
     * @param DomainEvent $event
     */
    public function onDomainDeleted(DomainEvent $event)
    {
        $deletedDomain = new DeletedDomain(new HivDomainValue($event->getDomain()->getName()));
        $this->deletedDomainRepo->persist($deletedDomain)->flush();
    }
}
