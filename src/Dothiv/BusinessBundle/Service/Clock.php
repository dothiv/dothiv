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
     * @param string $clockExpr
     */
    public function __construct($clockExpr)
    {
        $this->clock = $clockExpr instanceof \DateTime ? $clockExpr : $this->createDate($clockExpr);
    }

    /**
     * @param $clockExpr
     *
     * @return \DateTime
     */
    protected function createDate($clockExpr)
    {
        $d = new \DateTime($clockExpr);
        $d->setTimezone(new \DateTimeZone('Europe/Berlin'));
        return $d;
    }

    /**
     * @return \DateTime
     */
    public function getNow()
    {
        return clone $this->clock;
    }
} 
