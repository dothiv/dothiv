<?php


namespace Dothiv\BaseWebsiteBundle\Tests\Twig\Extension;

use Dothiv\BaseWebsiteBundle\Service\MoneyFormatService;
use Dothiv\BaseWebsiteBundle\Service\NumberFormatService;
use Dothiv\BaseWebsiteBundle\Twig\Extension\MoneyTwigExtension;

/**
 * Test for the money and decimalMoney Twig Filters.
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class MoneyTwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group BaseWebsiteBundle
     * @group TwigExtension
     * @group MoneyFormatService
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Twig\Extension\MoneyTwigExtension', $this->getTestObject());
    }

    /**
     * @test
     * @group        BaseWebsiteBundle
     * @group        TwigExtension
     * @group        MoneyFormatService
     * @depends      itShouldBeInstantiable
     */
    public function itShouldFormatMoney()
    {
        $this->assertEquals('500.000,00 €', $this->getTestObject()->money(array('locale' => 'en'), '500000', 'de'));
    }

    /**
     * @test
     * @group        BaseWebsiteBundle
     * @group        TwigExtension
     * @group        MoneyFormatService
     * @depends      itShouldBeInstantiable
     */
    public function itShouldFormatDecimalMoney()
    {
        $this->assertEquals('500.000 €', $this->getTestObject()->decimalMoney(array('locale' => 'en'), '500000', 'de'));
    }

    /**
     * @return MoneyTwigExtension
     */
    protected function getTestObject()
    {
        return new MoneyTwigExtension(new MoneyFormatService(new NumberFormatService()));
    }
}
