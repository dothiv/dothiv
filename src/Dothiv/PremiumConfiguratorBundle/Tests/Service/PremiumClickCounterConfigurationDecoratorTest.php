<?php

namespace Dothiv\PremiumConfiguratorBundle\Tests\Service;

use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Service\LinkableAttachmentStoreInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Service\PremiumClickCounterConfigurationDecorator;
use Dothiv\ValueObject\HexValue;
use Dothiv\ValueObject\URLValue;

class PremiumClickCounterConfigurationDecoratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LinkableAttachmentStoreInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockAttachmentStore;

    /**
     * @test
     * @group        Listener
     * @group        ClickCounterConfig
     * @group        PremiumConfiguratorBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\PremiumConfiguratorBundle\Service\PremiumClickCounterConfigurationDecorator', $this->createTestObject());
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
        $subscription = new Subscription();
        $subscription->setDomain($domain);
        $customer     = new \Stripe_Customer();
        $customer->id = 'some-id';
        $subscription->activate($customer);
        $banner = new Banner();
        $banner->setDomain($domain);
        $domain->setActiveBanner($banner);
        $premiumBanner = new PremiumBanner();
        $premiumBanner->setFontColor(new HexValue("#ffff01"));
        $premiumBanner->setBgColor(new HexValue("#ffffff"));
        $premiumBanner->setBarColor(new HexValue("#3482ab"));
        $bgAttachment = new Attachment();
        $bgUrl        = new URLValue('http://example.com/bg');
        $premiumBanner->setBg($bgAttachment);
        $visualAttachment = new Attachment();
        $visualUrl        = new URLValue('http://example.com/visual');
        $visualMicroUrl   = new URLValue('http://example.com/visual-micro');
        $premiumBanner->setVisual($visualAttachment);
        $premiumBanner->setHeadlineFont("Source Sans Pro");
        $premiumBanner->setHeadlineFontWeight("300");
        $premiumBanner->setHeadlineFontSize(24);
        $premiumBanner->setTextFont("Source Code Pro");
        $premiumBanner->setTextFontWeight("200");
        $premiumBanner->setTextFontSize(17);

        // Set up mocks
        $attachmentStoreMap = array(
            array($bgAttachment, 'image/*;scale=bg', $bgUrl),
            array($visualAttachment, 'image/*;scale=visual', $visualUrl),
            array($visualAttachment, 'image/*;scale=visual-micro', $visualMicroUrl),
        );
        $this->mockAttachmentStore->expects($this->atLeastOnce())->method('getUrl')
            ->will($this->returnValueMap($attachmentStoreMap));

        // Run
        $updatedConfig = $this->createTestObject()->decorate(array(), $premiumBanner);
        $this->assertArrayHasKey('premium', $updatedConfig);
        $this->assertTrue($updatedConfig['premium']);
        $premiumConfig = array(
            "fontColor"          => "#FFFF01",
            "bgColor"            => "#FFFFFF",
            "barColor"           => "#3482AB",
            "bg"                 => (string)$bgUrl,
            "visual"             => (string)$visualUrl,
            "visual@micro"       => (string)$visualMicroUrl,
            "headlineFont"       => "Source Sans Pro",
            "headlineFontWeight" => "300",
            "headlineFontSize"   => 24,
            "textFont"           => "Source Code Pro",
            "textFontWeight"     => "200",
            "textFontSize"       => 17
        );
        foreach ($premiumConfig as $k => $v) {
            $this->assertEquals($updatedConfig[$k], $v);
        }
    }

    protected function createTestObject()
    {
        return new PremiumClickCounterConfigurationDecorator(
            $this->mockAttachmentStore
        );
    }

    public function setUp()
    {
        $this->mockAttachmentStore
            = $this->getMock('\Dothiv\BusinessBundle\Service\LinkableAttachmentStoreInterface');
    }
} 
