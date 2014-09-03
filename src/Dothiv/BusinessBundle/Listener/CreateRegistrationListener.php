<?php

namespace Dothiv\BusinessBundle\Listener;

use Dothiv\AfiliasImporterBundle\Event\DomainRegisteredEvent;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Service\IRegistration;

/**
 * Creates a new domain entity via the registration service for every new domain registration
 */
class CreateRegistrationListener
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
     * @param DomainRegisteredEvent $event
     */
    public function onDomainRegistered(DomainRegisteredEvent $event)
    {
        if ($this->domainRepo->getDomainByName($event->DomainName)->isEmpty()) {
            $this->registrationService->registered(
                $event->DomainName,
                $event->RegistrantEmail,
                $event->RegistrantName,
                $event->RegistrarExtId
            );
        }
    }
} 
