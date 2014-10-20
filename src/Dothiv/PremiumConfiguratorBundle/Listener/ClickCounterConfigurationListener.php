<?php

namespace Dothiv\PremiumConfiguratorBundle\Listener;

use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Repository\PremiumBannerRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Service\PremiumClickCounterConfigurationDecoratorInterface;

/**
 * Activates the premium click-counter configuration, if a subscription exists
 */
class ClickCounterConfigurationListener
{

    /**
     * @var SubscriptionRepositoryInterface
     */
    private $subscriptionRepo;

    /**
     * @var PremiumBannerRepositoryInterface
     */
    private $premiumBannerRepo;

    /**
     * @var PremiumClickCounterConfigurationDecoratorInterface
     */
    private $configDecorator;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepo,
        PremiumBannerRepositoryInterface $premiumBannerRepo,
        PremiumClickCounterConfigurationDecoratorInterface $configDecorator)
    {
        $this->subscriptionRepo  = $subscriptionRepo;
        $this->premiumBannerRepo = $premiumBannerRepo;
        $this->configDecorator   = $configDecorator;
    }

    /**
     * @param ClickCounterConfigurationEvent $event
     */
    public function onClickCounterConfiguration(ClickCounterConfigurationEvent $event)
    {
        $subscriptionOptional = $this->subscriptionRepo->findByDomain($event->getDomain());
        if ($subscriptionOptional->isEmpty()) {
            return;
        }
        /** @var Subscription $subscription */
        $subscription = $subscriptionOptional->get();
        if (!$subscription->isActive()) {
            return;
        }
        $premiumBannerOptional = $this->premiumBannerRepo->findByBanner($event->getDomain()->getActiveBanner());
        if ($premiumBannerOptional->isEmpty()) {
            return;
        }
        $config = $this->configDecorator->decorate($event->getConfig(), $premiumBannerOptional->get());
        $event->setConfig($config);
    }
} 
