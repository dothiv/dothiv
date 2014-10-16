<?php

namespace Dothiv\CharityWebsiteBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\CharityWebsiteBundle\Entity\DomainConfigurationNotification;
use Dothiv\CharityWebsiteBundle\Exception\InvalidArgumentException;

interface DomainConfigurationNotificationRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param DomainConfigurationNotification $domainConfigurationNotification
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(DomainConfigurationNotification $domainConfigurationNotification);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param Domain $domain
     *
     * @return DomainConfigurationNotification[]|ArrayCollection
     */
    public function findByDomain(Domain $domain);
} 
