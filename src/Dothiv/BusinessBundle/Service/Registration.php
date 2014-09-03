<?php

namespace Dothiv\BusinessBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\RegistrarRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * {@inheritdoc}
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 * @author Markus Tacker <m@dothiv.org>
 */
class Registration implements IRegistration
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var RegistrarRepositoryInterface
     */
    private $registrarRepo;

    /**
     * @param EventDispatcherInterface     $eventDispatcher
     * @param DomainRepositoryInterface    $domainRepo
     * @param RegistrarRepositoryInterface $registrarRepo
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DomainRepositoryInterface $domainRepo,
        RegistrarRepositoryInterface $registrarRepo
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->domainRepo      = $domainRepo;
        $this->registrarRepo   = $registrarRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function registered($name, $ownerEmail, $ownerName, $registrarExtId)
    {
        // check if domain is already known
        if ($this->domainRepo->getDomainByName($name)->isDefined()) {
            throw new RegistrationException('Tried to register already registered domain ' . $name . ' for ' . $ownerEmail . '.');
        }

        // Find registrar
        $registrarRepo = $this->registrarRepo;
        $registrar     = $registrarRepo->findByExtId($registrarExtId)->getOrCall(function () use ($registrarExtId, $registrarRepo) {
            $registrar = new Registrar();
            $registrar->setExtId($registrarExtId);
            $registrarRepo->persist($registrar)->flush();
            return $registrar;
        });

        // create domain object
        $d = new Domain();
        $d->setName($name);
        $d->setOwnerEmail($ownerEmail);
        $d->setOwnerName($ownerName);
        $d->setToken($this->generateToken());
        $d->setRegistrar($registrar);

        // save domain
        $this->domainRepo->persist($d)->flush();

        // Dispatch event.
        $this->eventDispatcher->dispatch(BusinessEvents::DOMAIN_REGISTERED, new DomainEvent($d));

        return $d;
    }

    /**
     * {@inheritdoc}
     */
    public function deleted($name)
    {
        $optionalDomain = $this->domainRepo->getDomainByName($name);
        if ($optionalDomain->isEmpty()) {
            throw new RegistrationException('Tried to delete not-registered domain ' . $name . '.');
        }
        $domain = $optionalDomain->get();
        $this->domainRepo->remove($domain)->flush();

        // Dispatch event.
        $this->eventDispatcher->dispatch(BusinessEvents::DOMAIN_DELETED, new DomainEvent($domain));
    }

    /**
     * {@inheritdoc}
     */
    public function transferred($name, $ownerEmail, $ownerName, $registrarExtId)
    {
        $this->deleted($name);
        $domain = $this->registered($name, $ownerEmail, $ownerName, $registrarExtId);

        // Dispatch event.
        $this->eventDispatcher->dispatch(BusinessEvents::DOMAIN_TRANSFERRED, new DomainEvent($domain));

        return $domain;
    }

    private function generateToken()
    {
        $sr = new SecureRandom();
        return bin2hex($sr->nextBytes(8));
    }
}
