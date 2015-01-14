<?php

namespace Dothiv\CharityWebsiteBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\CharityWebsiteBundle\Entity\DomainNotification;
use Dothiv\CharityWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\ValueObject\IdentValue;

interface DomainNotificationRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param DomainNotification $domainNotification
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(DomainNotification $domainNotification);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param Domain     $domain
     * @param IdentValue $type Type of notification
     *
     * @return DomainNotification[]|ArrayCollection
     */
    public function findByDomain(Domain $domain, IdentValue $type);
}
