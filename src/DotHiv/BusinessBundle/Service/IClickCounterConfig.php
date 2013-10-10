<?php

namespace DotHiv\BusinessBundle\Service;

use DotHiv\BusinessBundle\Entity\Banner;
use DotHiv\BusinessBundle\Entity\Domain;

/**
 * Provides communication to the banner platform.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
interface IClickCounterConfig {

    /**
     * Sets a the banner/click counter configuration for the domain.
     *
     * @param Domain $domain The domain to be configured
     * @param Banner $banner The banner/click counter configuration to set up
     */
    function setup(Domain $domain, Banner $banner);

    /**
     * Removes all banner/click counter configuration from the given domain.
     *
     * @param Domain $domain
     */
    function reset(Domain $domain);

    /**
     * Retrieves information about the given domain from the banner/
     * click counter cloud application.
     *
     * @param Domain $domain The domain to enquiry about.
     */
    function retrieveByDomain(Domain $domain);

    /**
     * Retrieves information about all domains that were not updated
     * since the given date.
     *
     * @param \DateTime $notSince All domains that were updated for the
     * last time before that date will be updated.
     */
    function retrieveByDate(\DateTime $notSince);

}
