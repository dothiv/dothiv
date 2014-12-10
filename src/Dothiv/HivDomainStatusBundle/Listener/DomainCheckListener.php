<?php


namespace Dothiv\HivDomainStatusBundle\Listener;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use Dothiv\HivDomainStatusBundle\Event\DomainCheckEvent;
use Dothiv\HivDomainStatusBundle\Repository\HivDomainCheckRepositoryInterface;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;

/**
 * This listener persists the check result for domains
 */
class DomainCheckListener
{

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var HivDomainCheckRepositoryInterface
     */
    private $checkRepo;

    /**
     * @param DomainRepositoryInterface         $domainRepo
     * @param HivDomainCheckRepositoryInterface $checkRepo
     */
    public function __construct(DomainRepositoryInterface $domainRepo, HivDomainCheckRepositoryInterface $checkRepo)
    {
        $this->domainRepo = $domainRepo;
        $this->checkRepo  = $checkRepo;
    }

    public function onDomainCheck(DomainCheckEvent $event)
    {
        $check          = $event->getCheck();
        $domainOptional = $this->domainRepo->getDomainByName($check->domain);
        if ($domainOptional->isEmpty()) {
            return;
        }
        /** @var Domain $domain */
        $domain = $domainOptional->get();

        $hivDomainCheck = new HivDomainCheck();
        if ($check->addresses !== null) {
            $hivDomainCheck->setAddresses($check->addresses);
        }
        $hivDomainCheck->setDnsOk($check->dnsOk);
        $hivDomainCheck->setDomain($domain);
        $hivDomainCheck->setIframePresent($check->iframePresent);
        $hivDomainCheck->setIframeTarget($check->iframeTarget);
        $hivDomainCheck->setIframeTargetOk($check->iframeTargetOk);
        $hivDomainCheck->setScriptPresent($check->scriptPresent);
        $hivDomainCheck->setStatusCode($check->statusCode);
        $hivDomainCheck->setUrl($check->url);
        $hivDomainCheck->setValid($check->valid);

        $lastCheckOptional = $this->checkRepo->findLatestForDomain($domain);
        if ($lastCheckOptional->isDefined()) {
            /** @var HivDomainCheck $lastCheck */
            $lastCheck = $lastCheckOptional->get();
            if ($lastCheck->equals($hivDomainCheck)) {
                return;
            }
        }
        $this->checkRepo->persist($hivDomainCheck)->flush();
    }
}
