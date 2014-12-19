<?php


namespace Dothiv\ShopBundle\Test\Service;

use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\ShopBundle\Entity\DomainInfo;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Repository\DomainInfoRepositoryInterface;
use Dothiv\ShopBundle\Service\DomainPriceService;
use Dothiv\ValueObject\HivDomainValue;

class DomainPriceServiceTest extends \PHPUnit_Framework_TestCase
{

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
     * @param int    $expectedEurPrice
     * @param int    $expectedUsdPrice
     *
     * @dataProvider domainPriceDataProvider
     */
    public function itShouldCalculateTheDomainPrice($domain, $expectedEurPrice, $expectedUsdPrice)
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
        $priceEur = $newConfig('shop.price.eur', 14500);
        $priceUsd = $newConfig('shop.price.usd', 18000);
        $modEur   = $newConfig('shop.promo.name4life.eur.mod', -13000);
        $modUsd   = $newConfig('shop.promo.name4life.usd.mod', -16100);

        $configMap = array(
            array($priceEur->getName(), $priceEur),
            array($priceUsd->getName(), $priceUsd),
            array($modEur->getName(), $modEur),
            array($modUsd->getName(), $modUsd),
        );
        $this->mockConfigRepo->expects($this->any())->method('get')
            ->will($this->returnValueMap($configMap));

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
            array('caro.hiv', 14500, 18000),
            array('caro4life.hiv', 1500, 1900),
        );
    }

    /**
     * @return DomainPriceService
     */
    protected function createTestObject()
    {
        return new DomainPriceService($this->mockConfigRepo);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockConfigRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface');
    }
}
