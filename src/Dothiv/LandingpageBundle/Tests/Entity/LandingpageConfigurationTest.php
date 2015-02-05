<?php

namespace Dothiv\LandingpageBundle\Entity\Tests;

use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\ValueObject\IdentValue;

class LandingpageConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group ShopBundle
     * @group LandingpageConfiguration
     * @group Shop
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Entity\LandingpageConfiguration', $this->createTestObject());
    }

    /**
     * @test
     * @group                    Entity
     * @group                    ShopBundle
     * @group                    LandingpageConfiguration
     * @group                    Shop
     * @depends                  itShouldBeInstantiateable
     * @expectedException        \Dothiv\LandingpageBundle\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid language provided: "pl"
     */
    public function testInvalidLanguage()
    {
        $this->createTestObject()->setLanguage(new IdentValue('pl'));
    }

    /**
     * @return LandingpageConfiguration
     */
    protected function createTestObject()
    {
        return new LandingpageConfiguration();
    }
}
