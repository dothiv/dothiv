<?php


namespace Dothiv\LandingpageBundle\Test\Service;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface;
use Dothiv\LandingpageBundle\Service\LandingpageService;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\None;

class LandingpageServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LandingpageConfigurationRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLandingpageRepo;

    /**
     * @test
     * @group Landingpage
     * @group LandingpageBundle
     * @group Service
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Service\LandingpageService', $this->createTestObject());
    }

    /**
     * @test
     * @group        Landingpage
     * @group        LandingpageBundle
     * @group        Service
     * @depends      itShouldBeInstantiable
     * @dataProvider getTestData
     *
     * @param string  $domain
     * @param boolean $expectedResult
     */
    public function testHasLandingpage($domain, $expectedResult)
    {
        $d = new Domain();
        $d->setName($domain);
        $this->assertEquals($expectedResult, $this->createTestObject()->hasLandingpage($d));
    }

    /**
     * @test
     * @group        Landingpage
     * @group        LandingpageBundle
     * @group        Service
     * @depends      itShouldBeInstantiable
     * @dataProvider getTestData
     *
     * @param string  $domain
     * @param boolean $expectedResult
     */
    public function testQualifiesForLandingpage($domain, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->createTestObject()->qualifiesForLandingpage(new HivDomainValue($domain)));
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        return [
            ['caro4life.hiv', true],
            ['caro.hiv', false],
            ['4life.hiv', false]
        ];
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

        $this->mockLandingpageRepo->expects($this->once())->method('findByDomain')
            ->with($domain)
            ->willReturn(None::create());

        $this->mockLandingpageRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (LandingpageConfiguration $c) {
                return true;
            }))
            ->willReturnSelf();
        $this->mockLandingpageRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $this->createTestObject()->createLandingPageForShopOrder($order, $domain);
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
        $order = new Order();
        $order->setLandingpageOwner('Caro');

        $this->mockLandingpageRepo->expects($this->never())->method('persist');

        $this->createTestObject()->createLandingPageForShopOrder($order, $domain);
    }

    /**
     * @return LandingpageService
     */
    protected function createTestObject()
    {
        return new LandingpageService($this->mockLandingpageRepo);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockLandingpageRepo = $this->getMock('\Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface');
    }
}
