<?php

namespace Dothiv\LandingpageBundle\Listener;

use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface;
use Dothiv\LandingpageBundle\Service\LandingpageConfigServiceInterface;

/**
 * This adds landing page configuration settings to the iFrame config
 *
 * @see Dothiv\BusinessBundle\Listener\ClickCounterIframeConfigListener
 */
class IframeConfigListener
{
    /**
     * @param LandingpageConfigurationRepositoryInterface $configRepo
     * @param LandingpageConfigServiceInterface           $configService
     */
    public function __construct(
        LandingpageConfigurationRepositoryInterface $configRepo,
        LandingpageConfigServiceInterface $configService
    )
    {
        $this->configRepo    = $configRepo;
        $this->configService = $configService;
    }

    /**
     * @param ClickCounterConfigurationEvent $event
     */
    public function onClickCounterConfiguration(ClickCounterConfigurationEvent $event)
    {

        $domain         = $event->getDomain();
        $configOptional = $this->configRepo->findByDomain($domain);
        if ($configOptional->isEmpty()) {
            return;
        }
        /** @var LandingpageConfiguration $config */
        $config                      = $configOptional->get();
        $iframeConfig                = $event->getConfig();
        $iframeConfig['landingPage'] = $this->configService->buildConfig($config);
        $event->setConfig($iframeConfig);
    }
}
