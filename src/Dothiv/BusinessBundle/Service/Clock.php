<?php

namespace Dothiv\BusinessBundle\Service;

use PhpOption\Option;

/**
 * The clock service abstracts the current date.
 */
class Clock
{
    /**
     * @var \DateTime
     */
    private $clock;

    /**
     * @param \DateTime $clock
     */
    public function __construct(\DateTime $clock = null)
    {
        $this->clock = Option::fromValue($clock)->getOrElse(new \DateTime());
    }

    /**
     * @return \DateTime
     */
    public function getNow()
    {
        return clone $this->clock;
    }
} 
