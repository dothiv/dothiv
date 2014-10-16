<?php

namespace Dothiv\CharityWebsiteBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\ValueObject\HivDomainValue;

/**
 * Notifies domain owners that their click-counter has not yet been installed.
 */
interface SendClickCounterConfigurationServiceInterface
{
    /**
     * Send the click-counter configuration to the owner of the domain
     *
     * @param HivDomainValue $domain
     */
    public function sendConfiguration(HivDomainValue $domain);

    /**
     * Send the click-counter configuration to the owner of the domain
     *
     * @param Domain $domain
     */
    public function sendConfigurationForDomain(Domain $domain);

    /**
     * Returns a list of domains which need to be notified.
     *
     * @return Domain[]|ArrayCollection
     */
    public function findDomainsToBeNotified();
} 
