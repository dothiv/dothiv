<?php


namespace Dothiv\ShopBundle\Test\Service;

use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\LandingpageBundle\Service\LandingpageServiceInterface;
use Dothiv\ShopBundle\Service\DomainPriceService;
use Dothiv\ValueObject\HivDomainValue;

class DomainPriceServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LandingpageServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLandingpageService;

    /**
     * @var ConfigRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConfigRepo;

    /**
     * @test
     * @group Shop
     * @group Service
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\ShopBundle\Service\DomainPriceService', $this->createTestObject());
    }

    /**
     * @test
     * @group        Shop
     * @group        Service
     * @depends      itShouldBeInstantiable
     *
     * @param string $domain
     * @param bool   $promoDomain
     * @param int    $expectedEurPrice
     * @param int    $expectedUsdPrice
     *
     * @dataProvider domainPriceDataProvider
     */
    public function itShouldCalculateTheDomainPrice($domain, $promoDomain, $expectedEurPrice, $expectedUsdPrice)
    {
        $newConfig = function ($name, $value) {
            $config = new Config();
            $config->setName($name);
            $config->setValue($value);
            return $config;
        };
        /** @var Config $priceEur */
        /** @var Config $priceUsd */
        /** @var Config $modEur */
        /** @var Config $modUsd */
        /** @var Config $enable */
        $priceEur = $newConfig('shop.price.eur', 14500);
        $priceUsd = $newConfig('shop.price.usd', 18000);
        $modEur   = $newConfig('shop.promo.name4life.eur.mod', -13000);
        $modUsd   = $newConfig('shop.promo.name4life.usd.mod', -16100);
        $enable   = $newConfig('shop.promo.name4life.enable', 1);

        $configMap = array(
            array($priceEur->getName(), $priceEur),
            array($priceUsd->getName(), $priceUsd),
            array($modEur->getName(), $modEur),
            array($modUsd->getName(), $modUsd),
            array($enable->getName(), $enable),
        );
        $this->mockConfigRepo->expects($this->any())->method('get')
            ->will($this->returnValueMap($configMap));

        $this->mockLandingpageService->expects($this->once())->method('qualifiesForLandingpage')
            ->with(HivDomainValue::create($domain))
            ->willReturn($promoDomain);

        $price = $this->createTestObject()->getPrice(new HivDomainValue($domain));
        $this->assertEquals($expectedEurPrice, $price->getNetPriceEUR());
        $this->assertEquals($expectedUsdPrice, $price->getNetPriceUSD());
    }

    /**
     * @return array
     */
    public function domainPriceDataProvider()
    {
        return array(
            array('caro.hiv', false, 14500, 18000),
            array('caro4life.hiv', true, 1500, 1900),
        );
    }

    /**
     * @return DomainPriceService
     */
    protected function createTestObject()
    {
        return new DomainPriceService($this->mockConfigRepo, $this->mockLandingpageService);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockConfigRepo         = $this->getMock('\Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface');
        $this->mockLandingpageService = $this->getMock('\Dothiv\LandingpageBundle\Service\LandingpageServiceInterface');
    }
}
