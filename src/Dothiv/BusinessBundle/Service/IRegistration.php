<?php

namespace Dothiv\BusinessBundle\Service;

/**
 * Executes the actions to be taken at dotHIV if
 * a hiv-domain is registered, deleted or transferred.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 * @author Markus Tacker <m@dothiv.org>
 */
interface IRegistration
{

    /**
     * Call this function for a newly registered domain.
     *
     * @param string $name           The domain name, e.g. 'mydomain.hiv'.
     * @param string $ownerEmail     The domain owner's email address.
     * @param string $ownerName      The domain owner's name.
     * @param string $registrarExtId The extId of the registrar
     */
    function registered($name, $ownerEmail, $ownerName, $registrarExtId);

    /**
     * Call this function for domains that have been deleted.
     *
     * @param string $name The domain name, e.g. 'mydomain.hiv'.
     */
    function deleted($name);

    /**
     * Call this function for domains that have been transferred.
     *
     * @param string $name           The domain name, e.g. 'mydomain.hiv'.
     * @param string $ownerEmail     The new domain owner's email address.
     * @param string $ownerName      The new domain owner's name.
     * @param string $registrarExtId The extId of the registrar
     */
    function transferred($name, $ownerEmail, $ownerName, $registrarExtId);

}
