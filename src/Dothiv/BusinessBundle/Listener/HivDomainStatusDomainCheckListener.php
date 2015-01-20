<?php


namespace Dothiv\BusinessBundle\Listener;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\HivDomainStatusBundle\Event\DomainCheckEvent;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;

/**
 * This listener sets a flag on the Domain entity for the .hiv domain status.
 */
class HivDomainStatusDomainCheckListener
{

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var EntityChangeRepositoryInterface
     */
    private $changeRepo;

    /**
     * @param DomainRepositoryInterface       $domainRepo
     * @param EntityChangeRepositoryInterface $changeRepo
     */
    public function __construct(DomainRepositoryInterface $domainRepo, EntityChangeRepositoryInterface $changeRepo)
    {
        $this->domainRepo = $domainRepo;
        $this->changeRepo = $changeRepo;
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
        if ($domain->isLive() != $check->valid) {
            if ($check->valid) {
                $domain->enliven(new \DateTime($check->created));
            } else {
                $domain->kill();
            }
            $this->domainRepo->persist($domain)->flush();
            $change = new EntityChange();
            $change->setAuthor(new EmailValue('HivDomainStatusDomainCheckListener@business.bundle'));
            $change->setEntity($this->domainRepo->getItemEntityName($domain));
            $change->setIdentifier(new IdentValue($domain->getPublicId()));
            $change->addChange(new IdentValue('live'), !$domain->isLive(), $domain->isLive());
            $this->changeRepo->persist($change)->flush();
        }
    }
}
