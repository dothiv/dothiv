<?php

namespace Dothiv\BusinessBundle\Event;

use Dothiv\BusinessBundle\Entity\Domain;
use Symfony\Component\EventDispatcher\Event;

class DomainEvent extends Event
{
    /**
     * @var Domain
     */
    private $domain;

    /**
     * @param Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param Domain $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
