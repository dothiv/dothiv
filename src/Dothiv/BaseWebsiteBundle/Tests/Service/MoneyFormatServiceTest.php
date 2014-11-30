<?php

namespace Dothiv\BaseWebsiteBundle\Tests\Service;

use Dothiv\BaseWebsiteBundle\Service\MoneyFormatService;
use Dothiv\BaseWebsiteBundle\Service\NumberFormatService;

/**
 * Test for the MoneyFormatService
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class MoneyFormatServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group BaseWebsiteBundle
     * @group Service
     * @group MoneyFormatService
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Service\MoneyFormatService', $this->getTestObject());
    }

    /**
     * @test
     * @group            BaseWebsiteBundle
     * @group            Service
     * @group            MoneyFormatService
     * @depends          itShouldBeInstantiable
     * @dataProvider     getDecimalTestData
     */
    public function itShouldFormatDecimalMoney($amount, $locale, $expected)
    {
        $this->assertEquals($expected, $this->getTestObject()->decimalFormat($amount, $locale));
    }

    /**
     * @test
     * @group            BaseWebsiteBundle
     * @group            Service
     * @group            MoneyFormatService
     * @depends          itShouldBeInstantiable
     * @dataProvider     getTestData
     */
    public function itShouldFormatMoney($amount, $locale, $expected)
    {
        $this->assertEquals($expected, $this->getTestObject()->format($amount, $locale));
    }

    /**
     * Dataprovider for itShouldFormatDecimalMoney
     *
     * @return array
     */
    public function getDecimalTestData()
    {
        return array(
            array(500000, 'de', '500.000 €'),
            array('500000', 'de', '500.000 €'),
            array(500000, 'en', '€500,000'),
            array('500000', 'en', '€500,000'),
            array(500000, null, '€500,000'),
            array('500000', null, '€500,000'),
        );
    }

    /**
     * Dataprovider for itShouldFormatMoney
     *
     * @return array
     */
    public function getTestData()
    {
        return array(
            array(500000, 'de', '500.000,00 €'),
            array('500000', 'de', '500.000,00 €'),
            array(500000, 'en', '€500,000.00'),
            array('500000', 'en', '€500,000.00'),
            array(500000, null, '€500,000.00'),
            array('500000', null, '€500,000.00'),
            array(0.001, 'de', '0,1 ct'),
            array(0.001, 'en', '€0.1¢'),
            array(0.001, null, '€0.1¢'),
            array(0.01, 'de', '1 ct'),
            array(0.01, 'en', '€1¢'),
            array(0.01, null, '€1¢'),
        );
    }

    protected function getTestObject()
    {
        return new MoneyFormatService(new NumberFormatService());
    }
}
