<?php

namespace Dothiv\BusinessBundle\Service;

/**
 * Executes the actions to be taken at dotHIV if
 * a hiv-domain is registered, deleted or transferred.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
interface IRegistration {

    /**
     * Call this function for a newly registered domain.
     *
     * @param string $name The domain name, e.g. 'mydomain.hiv'.
     * @param string $email The domain owner's email address.
     */
    function registered($name, $email);

    /**
     * Call this function for domains that have been deleted.
     *
     * @param string $name The domain name, e.g. 'mydomain.hiv'.
     */
    function deleted($name);

    /**
     * Call this function for domains that have been transferred.
     *
     * @param string $name The domain name, e.g. 'mydomain.hiv'.
     * @param string $email The new domain owner's email address.
     */
    function transferred($name, $email);

}
