<?php

namespace Dothiv\BaseWebsiteBundle\Tests\Twig\Extension;

use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\BaseWebsiteBundle\Twig\Extension\ConfigTwigExtension;
use Dothiv\BusinessBundle\Entity\Config;

class ConfigTwigExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Doctrine\ORM\EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConfigRepo;

    /**
     * @test
     * @group BaseWebsiteBundle
     * @group TwigExtension
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Twig\Extension\ConfigTwigExtension', $this->getTestObject());
    }

    public function configValuesDataProvider()
    {
        return array(
            array('1.25', null, '1.25'),
            array('1.25', 'float', 1.25),
            array('1.25', 'f', 1.25),
            array('1.25', 'int', 1),
            array('1.25', 'integer', 1),
            array('1.25', 'd', 1),
            array('1.25', 'b', 'true'),
            array('', 'bool', 'false'),
            array('0', 'bool', 'false'),
        );
    }

    /**
     * @test
     * @group        BaseWebsiteBundle
     * @group        TwigExtension
     *
     * @dataProvider configValuesDataProvider
     *
     * @param             string    String-value of the config entry
     * @param string|null type      cast directive
     * @param mixed       $expValue expected value
     */
    public function itShouldReturnAConfigValue($strValue, $cast, $expValue)
    {
        $this->mockConfigRepo->expects($this->once())->method('get')
            ->with('the_key')
            ->willReturnCallback(function() use($strValue) {
                $config = new Config();
                $config->setName('the_key');
                $config->setValue((string)$strValue);
                return $config;
            });

        $ext    = $this->getTestObject();
        $called = false;
        foreach ($ext->getFunctions() as $func) {
            /** @var \Twig_SimpleFunction $func */
            $this->assertInstanceOf('\Twig_SimpleFunction', $func);
            if ($func->getName() == 'config') {
                $v = call_user_func($func->getCallable(), 'the_key', $cast);
                $this->assertSame($expValue, $v);
                $called = true;
            }
        }
        $this->assertTrue($called, 'Function "config" was not called.');
    }

    protected function getTestObject()
    {
        return new ConfigTwigExtension($this->mockConfigRepo);
    }

    public function setUp()
    {
        $this->mockConfigRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
