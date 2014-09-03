<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;

/**
 * Provides communication to the banner platform.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 * @author Markus Tacker <m@click4life.hiv>
 */
interface ClickCounterConfigInterface
{
    /**
     * Sets a the banner/click counter configuration for the domain.
     *
     * @param Banner $banner The banner/click counter configuration to set up
     */
    function setup(Banner $banner);

    /**
     * Read the domain values from the click counter API
     *
     * @param Domain $domain
     */
    function get(Domain $domain);

    /**
     * Reads the total click count.
     *
     * @return int
     */
    function getClickCount();
}
