<?php


namespace Dothiv\LandingpageBundle\Service;

use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;

/**
 * This creates the landing page configuration to be used on the hosting microservice and the configurator preview
 */
interface LandingpageConfigServiceInterface
{
    /**
     * @param LandingpageConfiguration $config
     *
     * @return array
     */
    function buildConfig(LandingpageConfiguration $config);

}
