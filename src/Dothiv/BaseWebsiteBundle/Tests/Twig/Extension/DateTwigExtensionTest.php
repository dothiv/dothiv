<?php


namespace Dothiv\BaseWebsiteBundle\Tests\Twig\Extension;

use Dothiv\BaseWebsiteBundle\Twig\Extension\DateTwigExtension;

class DateTwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group BaseWebsiteBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Twig\Extension\DateTwigExtension', $this->getTestObject());
    }

    /**
     * @test
     * @group        BaseWebsiteBundle
     * @depends      itShouldBeInstantiable
     * @dataProvider getTestData
     */
    public function itShouldConvertADate($expected, $original)
    {
        $this->assertEquals($expected, $this->getTestObject()->dateW3c($original));
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        return array(
            array('2014-06-21T00:00:00+00:00', new \DateTime('2014-06-21T00:00:00Z')),
            array('2014-06-21T00:00:00+00:00', '2014-06-21T00:00:00Z'),
            array('2014-08-26T00:00:00+00:00', '1409011200'),
            array('2014-08-26T00:00:00+00:00', 1409011200)
        );
    }

    /**
     * @return DateTwigExtension
     */
    protected function getTestObject()
    {
        return new DateTwigExtension();
    }
}
