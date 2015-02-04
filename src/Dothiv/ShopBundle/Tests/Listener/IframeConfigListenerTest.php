<?php


namespace Dothiv\ShopBundle\Test\Listener;

use Dothiv\BaseWebsiteBundle\Contentful\ContentInterface;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Listener\IframeConfigListener;
use Dothiv\ShopBundle\Repository\OrderRepositoryInterface;
use Dothiv\ShopBundle\Service\GenitivfyService;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\Option;

class IframeConfigListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var OrderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOrderRepo;

    /**
     * @var ContentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConfig;

    /**
     * @test
     * @group Shop
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\ShopBundle\Listener\IframeConfigListener', $this->createTestObject());
    }

    /**
     * @test
     * @group   Shop
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldCreateLandingPageConfig()
    {
        $domain = new Domain();
        $domain->setName('caro4life.hiv');
        $order = new Order();
        $order->setLandingpageOwner('Caro');
        $config = [];

        $this->mockOrderRepo->expects($this->once())->method('findLatestByDomain')
            ->with(new HivDomainValue('caro4life.hiv'))
            ->willReturn(Option::fromValue($order));

        $this->mockConfig->expects($this->any())->method('buildEntry')
            ->with('String')
            ->willReturn((object)['value' => 'some string']);

        $event = new ClickCounterConfigurationEvent($domain, $config);
        $this->createTestObject()->onClickCounterConfiguration($event);
        $this->assertArrayHasKey('landingPage', $event->getConfig());
    }

    /**
     * @test
     * @group   Shop
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldNotCreateLandingPageConfigForRegularDomains()
    {
        $domain = new Domain();
        $domain->setName('caro.hiv');

        $this->mockOrderRepo->expects($this->never())->method('findLatestByDomain');

        $event = new ClickCounterConfigurationEvent($domain, []);
        $this->createTestObject()->onClickCounterConfiguration($event);
        $this->assertArrayNotHasKey('landingPage', $event->getConfig());
    }

    /**
     * @return IframeConfigListener
     */
    protected function createTestObject()
    {
        return new IframeConfigListener(
            $this->mockOrderRepo,
            $this->mockConfig,
            ['locales' => ['en', 'de', 'es', 'fr']],
            new GenitivfyService()
        );
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockOrderRepo = $this->getMock('\Dothiv\ShopBundle\Repository\OrderRepositoryInterface');
        $this->mockConfig    = $this->getMock('\Dothiv\BaseWebsiteBundle\Contentful\ContentInterface');
    }
}
