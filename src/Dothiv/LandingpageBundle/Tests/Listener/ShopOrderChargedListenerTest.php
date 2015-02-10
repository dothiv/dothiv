<?php


namespace Dothiv\LandingpageBundle\Test\Listener;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\LandingpageBundle\Listener\ShopOrderChargedListener;
use Dothiv\LandingpageBundle\Service\LandingpageServiceInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Event\OrderEvent;

class ShopOrderChargedListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LandingpageServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLandingpageService;

    /**
     * @test
     * @group Shop
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Listener\ShopOrderChargedListener', $this->createTestObject());
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

        $this->mockLandingpageService->expects($this->once())->method('createLandingPageForShopOrder')
            ->with($order);

        $event = new OrderEvent($order, $domain);
        $this->createTestObject()->onShopOrderCharged($event);
    }

    /**
     * @return ShopOrderChargedListener
     */
    protected function createTestObject()
    {
        return new ShopOrderChargedListener($this->mockLandingpageService);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockLandingpageService = $this->getMock('\Dothiv\LandingpageBundle\Service\LandingpageServiceInterface');
    }
}
