<?php

namespace Dothiv\BusinessBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Event\DomainEvent;
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
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ObjectManager            $om
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ObjectManager $om,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->om              = $om;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function registered($name, $ownerEmail, $ownerName)
    {
        // check if domain is already known
        if ($this->om->getRepository('DothivBusinessBundle:Domain')->findBy(array('name' => $name))) {
            throw new RegistrationException('Tried to register already registered domain ' . $name . ' for ' . $ownerEmail . '.');
        }

        // create domain object
        $d = new Domain();
        $d->setName($name);
        $d->setOwnerEmail($ownerEmail);
        $d->setOwnerName($ownerName);
        $d->setToken($this->generateToken());

        // save domain
        $this->om->persist($d);
        $this->om->flush();

        // Dispatch event.
        $this->eventDispatcher->dispatch(BusinessEvents::DOMAIN_REGISTERED, new DomainEvent($d));

        return $d;
    }

    /**
     * {@inheritdoc}
     */
    public function deleted($name)
    {
        $domains = $this->om->getRepository('DothivBusinessBundle:Domain')->findBy(array('name' => $name));

        if (count($domains) == 0) {
            throw new RegistrationException('Tried to delete not-registered domain ' . $name . '.');
        }

        $this->om->remove($domains[0]);
        $this->om->flush();

        // Dispatch event.
        $this->eventDispatcher->dispatch(BusinessEvents::DOMAIN_DELETED, new DomainEvent($domains[0]));
    }

    /**
     * {@inheritdoc}
     */
    public function transferred($name, $ownerEmail, $ownerName)
    {
        $this->deleted($name);
        $domain = $this->registered($name, $ownerEmail, $ownerName);

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
