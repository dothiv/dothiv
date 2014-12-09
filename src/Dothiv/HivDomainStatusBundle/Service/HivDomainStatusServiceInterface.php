<?php

namespace Dothiv\HivDomainStatusBundle\Service;

use Dothiv\BusinessBundle\Entity\Domain;

/**
 * Interface for the HIV domain status service API
 */
interface HivDomainStatusServiceInterface
{
    /**
     * Adds a domain to be checked by the service.
     *
     * @param Domain $domain
     */
    public function registerDomain(Domain $domain);

    /**
     * Removes a domain to be checked by the service.
     *
     * @param Domain $domain
     */
    public function unregisterDomain(Domain $domain);

    /**
     * Emits an event for every fetched domain.
     */
    public function fetchDomains();
}
