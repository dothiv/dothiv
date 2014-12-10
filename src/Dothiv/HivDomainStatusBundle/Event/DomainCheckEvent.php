<?php

namespace Dothiv\HivDomainStatusBundle\Event;

use Dothiv\HivDomainStatusBundle\Model\DomainCheckModel;
use Symfony\Component\EventDispatcher\Event;

class DomainCheckEvent extends Event
{
    /**
     * @var DomainCheckModel
     */
    private $check;

    /**
     * @param DomainCheckModel $check
     */
    public function __construct(DomainCheckModel $check)
    {
        $this->check = $check;
    }

    /**
     * @return DomainCheckModel
     */
    public function getCheck()
    {
        return $this->check;
    }
}
