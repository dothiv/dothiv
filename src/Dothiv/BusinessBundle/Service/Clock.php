<?php

namespace Dothiv\BusinessBundle\Service;

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
     * @param string $clockExpr
     */
    public function __construct($clockExpr)
    {
        $this->clock = $clockExpr instanceof \DateTime ? $clockExpr : new \DateTime($clockExpr);
    }

    /**
     * @return \DateTime
     */
    public function getNow()
    {
        return clone $this->clock;
    }
} 
