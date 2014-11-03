<?php


namespace Dothiv\BusinessBundle\Listener;

use Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\RegistrarRepositoryInterface;
use Dothiv\BusinessBundle\Service\IRegistration;
use Dothiv\BusinessBundle\Service\WhoisReportParser;
use Dothiv\BusinessBundle\Service\WhoisServiceInterface;
use Dothiv\ValueObject\HivDomainValue;

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
     * @var RegistrarRepositoryInterface
     */
    private $registrarRepo;

    /**
     * @var WhoisServiceInterface
     */
    private $whoisService;

    /**
     * @param IRegistration                $registrationService
     * @param DomainRepositoryInterface    $domainRepo
     * @param RegistrarRepositoryInterface $registrarRepo ,
     * @param WhoisServiceInterface        $whoisService
     */
    public function __construct(
        IRegistration $registrationService,
        DomainRepositoryInterface $domainRepo,
        RegistrarRepositoryInterface $registrarRepo,
        WhoisServiceInterface $whoisService
    )
    {
        $this->registrationService = $registrationService;
        $this->domainRepo          = $domainRepo;
        $this->registrarRepo       = $registrarRepo;
        $this->whoisService        = $whoisService;
    }

    /**
     * Deletes domains if the have been deleted.
     *
     * @param DomainTransactionEvent $event
     */
    public function onDomainDeleted(DomainTransactionEvent $event)
    {
        if ($this->domainRepo->getDomainByName($event->ObjectName)->isEmpty()) {
            return;
        }
        $this->registrationService->deleted($event->ObjectName);
    }

    /**
     * Marks domains as "in transfer" if they are transferred,
     * if the domain does not exist, a new domain is created
     *
     * A transfer for an unknown domain can happen for premium domains, which were batch-registered
     * but have just before the sunrise period, but not imported in the database.
     * Those domains are just created, but the token is not sent.
     *
     * @param DomainTransactionEvent $event
     */
    public function onDomainTransferred(DomainTransactionEvent $event)
    {
        /** @var Domain $domain */
        $domain = $this->domainRepo->getDomainByName($event->ObjectName)->getOrCall(function () use ($event) {
            // Find registrar
            $registrar     = $this->registrarRepo->getByExtId($event->RegistrarExtID);
            // Create Domain
            $reportParse = new WhoisReportParser();
            $domainWhois = $reportParse->parse($this->whoisService->lookup(new HivDomainValue($event->ObjectName)));
            $domain      = new Domain();
            $domain->setName($event->ObjectName);
            $domain->setOwnerEmail($domainWhois->get('Registrant Email'));
            $domain->setOwnerName($domainWhois->get('Registrant Name'));
            $domain->setRegistrar($registrar);
            return $domain;
        });
        $domain->transfer();
        $this->domainRepo->persist($domain)->flush();
    }
} 
