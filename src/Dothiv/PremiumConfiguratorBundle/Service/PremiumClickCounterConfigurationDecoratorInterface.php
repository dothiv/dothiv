<?php


namespace Dothiv\PremiumConfiguratorBundle\Service;

use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;

interface PremiumClickCounterConfigurationDecoratorInterface
{
    /**
     * Decorate the configuration in $config with premium settings
     *
     * @param array         $config
     * @param PremiumBanner $premiumBanner
     *
     * @return array
     */
    function decorate($config, PremiumBanner $premiumBanner);
} 
