<?php

namespace Dothiv\PremiumConfiguratorBundle\Tests\Listener;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Listener\ClickCounterConfigurationListener;
use Dothiv\PremiumConfiguratorBundle\Repository\PremiumBannerRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Service\PremiumClickCounterConfigurationDecoratorInterface;
use PhpOption\Option;

class ClickCounterConfigurationListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SubscriptionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockSubscriptionRepo;

    /**
     * @var PremiumBannerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockPremiumBannerRepo;

    /**
     * @var PremiumClickCounterConfigurationDecoratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConfigurationDecorator;

    /**
     * @test
     * @group        Listener
     * @group        ClickCounterConfig
     * @group        PremiumConfiguratorBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\PremiumConfiguratorBundle\Listener\ClickcounterConfigurationListener', $this->createTestObject());
    }

    /**
     * @test
     * @group        Listener
     * @group        ClickCounterConfig
     * @group        PremiumConfiguratorBundle
     * @depends      itShouldBeInstantiable
     */
    public function itShouldActivePremium()
    {
        $domain       = new Domain();
        $config       = array();
        $listener     = $this->createTestObject();
        $subscription = new Subscription();
        $subscription->setDomain($domain);
        $customer     = new \Stripe_Customer();
        $customer->id = 'some-id';
        $subscription->activate($customer);
        $banner = new Banner();
        $banner->setDomain($domain);
        $domain->setActiveBanner($banner);
        $premiumBanner = new PremiumBanner();

        // Set up mocks
        $this->mockSubscriptionRepo->expects($this->once())->method('findByDomain')
            ->with($domain)
            ->willReturn(Option::fromValue($subscription));

        $this->mockPremiumBannerRepo->expects($this->once())->method('findByBanner')
            ->with($banner)
            ->willReturn(Option::fromValue($premiumBanner));

        $this->mockConfigurationDecorator->expects($this->once())->method('decorate')
            ->with($config, $premiumBanner)
            ->willReturnArgument(0);

        // Run
        $event = new ClickCounterConfigurationEvent($domain, $config);
        $listener->onClickCounterConfiguration($event);
        $updatedConfig = $event->getConfig();
        $this->assertEquals($updatedConfig, $config);
    }

    protected function createTestObject()
    {
        return new ClickCounterConfigurationListener(
            $this->mockSubscriptionRepo,
            $this->mockPremiumBannerRepo,
            $this->mockConfigurationDecorator
        );
    }

    public function setUp()
    {
        $this->mockSubscriptionRepo
            = $this->getMock('\Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepositoryInterface');

        $this->mockPremiumBannerRepo
            = $this->getMock('\Dothiv\PremiumConfiguratorBundle\Repository\PremiumBannerRepositoryInterface');

        $this->mockConfigurationDecorator
            = $this->getMock('\Dothiv\PremiumConfiguratorBundle\Service\PremiumClickCounterConfigurationDecoratorInterface');
    }
} 
