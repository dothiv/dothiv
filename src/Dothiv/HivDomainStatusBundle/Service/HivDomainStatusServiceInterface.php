<?php

namespace Dothiv\HivDomainStatusBundle\Service;

use Dothiv\HivDomainStatusBundle\Exception\ServiceException;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\ValueObject\URLValue;

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
     * Fetches (new) check results and emits an event for every result
     *
     * @param URLValue|null $url
     *
     * @return URLValue Next Url
     *
     * @throws ServiceException
     */
    public function fetchChecks(URLValue $url = null);
}
