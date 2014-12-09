<?php

namespace Dothiv\HivDomainStatusBundle\Event;

use Dothiv\HivDomainStatusBundle\Model\DomainModel;
use Symfony\Component\EventDispatcher\Event;

class HivDomainStatusEvent extends Event
{

    /**
     * @var DomainModel
     */
    private $domain;

    /**
     * @param DomainModel $domain
     */
    public function __construct(DomainModel $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return DomainModel
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
