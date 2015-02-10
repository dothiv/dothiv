<?php

namespace Dothiv\LandingpageBundle\Listener;

use Dothiv\LandingpageBundle\Service\LandingpageServiceInterface;
use Dothiv\ShopBundle\Event\OrderEvent;

/**
 * Creates the landingpage configuration for new shop orders for 4life domains
 */
class ShopOrderChargedListener
{
    /**
     * @param LandingpageServiceInterface $landingpageService
     */
    public function __construct(LandingpageServiceInterface $landingpageService)
    {
        $this->landingpageService = $landingpageService;
    }

    /**
     * @param OrderEvent $event
     */
    public function onShopOrderCharged(OrderEvent $event)
    {
        $this->landingpageService->createLandingPageForShopOrder($event->getOrder(), $event->getDomain());
    }
}
