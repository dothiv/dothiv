<?php


namespace Dothiv\BusinessBundle\Service;

use Dothiv\ValueObject\HivDomainValue;

interface WhoisServiceInterface
{
    /**
     * Executes a WHOIS lookup for the given domain
     *
     * @param HivDomainValue $domain
     *
     * @return string
     */
    public function lookup(HivDomainValue $domain);
} 
