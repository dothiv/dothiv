<?php

namespace Dothiv\BaseWebsiteBundle\Tests\Service;

use Dothiv\BaseWebsiteBundle\Service\NumberFormatService;

/**
 * Test for the NumberFormatService
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class NumberFormatServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group BaseWebsiteBundle
     * @group Service
     * @group NumberFormatService
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Service\NumberFormatService', $this->getTestObject());
    }

    /**
     * @test
     * @group            BaseWebsiteBundle
     * @group            Service
     * @group            NumberFormatService
     * @depends          itShouldBeInstantiable
     * @dataProvider     getNumberTestData
     */
    public function itShouldFormatDecimalNumber($amount, $locale, $expected)
    {
        $this->assertEquals($expected, $this->getTestObject()->decimalFormat($amount, $locale));
    }

    /**
     * @test
     * @group            BaseWebsiteBundle
     * @group            Service
     * @group            NumberFormatService
     * @depends          itShouldBeInstantiable
     * @dataProvider     getTestData
     */
    public function itShouldFormatNumber($amount, $locale, $expected)
    {
        $this->assertEquals($expected, $this->getTestObject()->format($amount, $locale));
    }

    /**
     * Dataprovider for itShouldFormatNumberMoney
     *
     * @return array
     */
    public function getNumberTestData()
    {
        return array(
            array(500000, 'de', '500.000'),
            array('500000', 'de', '500.000'),
            array(500000, 'en', '500,000'),
            array('500000', 'en', '500,000'),
            array(500000, null, '500,000'),
            array('500000', null, '500,000'),
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
            array(500000, 'de', '500.000,00'),
            array('500000', 'de', '500.000,00'),
            array(500000, 'en', '500,000.00'),
            array('500000', 'en', '500,000.00'),
            array(500000, null, '500,000.00'),
            array('500000', null, '500,000.00'),
            array(0.001, 'de', '0,1'),
            array(0.001, 'en', '0.1'),
            array(0.001, null, '0.1'),
        );
    }

    protected function getTestObject()
    {
        return new NumberFormatService();
    }
}
