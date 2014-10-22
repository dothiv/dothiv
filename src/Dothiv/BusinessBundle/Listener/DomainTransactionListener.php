<?php


namespace Dothiv\BusinessBundle\Listener;

use Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Service\IRegistration;

/**
 * Acts on domain transactions events.
 */
class DomainTransactionListener
{

    /**
     * @var IRegistration
     */
    private $registrationService;

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @param IRegistration             $registrationService
     * @param DomainRepositoryInterface $domainRepo
     */
    public function __construct(IRegistration $registrationService, DomainRepositoryInterface $domainRepo)
    {
        $this->registrationService = $registrationService;
        $this->domainRepo          = $domainRepo;
    }

    /**
     * @param DomainTransactionEvent $event
     */
    public function onDomainDeleted(DomainTransactionEvent $event)
    {
        if ($this->domainRepo->getDomainByName($event->ObjectName)->isEmpty()) {
            return;
        }
        $this->registrationService->deleted($event->ObjectName);
    }
} 
