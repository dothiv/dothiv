<?php

namespace DotHiv\BusinessBundle\Service;

use DotHiv\BusinessBundle\Entity\Domain;

/**
 * Allows to change the dothiv DNS.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
interface IDnsConfig {

    /**
     * Configures the domain to be forwarded to the servers of dothiv.
     * 
     * @param Domain $domain
     */
    function forward(Domain $domain);

    /**
     * Resets the domain's DNS configuration to the defaults.
     *
     * @param Domain $domain
     */
    function reset(Domain $domain);

}
