<?php

namespace Dothiv\LandingpageBundle\Test\Service;

use Dothiv\LandingpageBundle\Service\GenitivfyService;
use Dothiv\ValueObject\IdentValue;

class GenitivfyServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group LandingpageBundle
     * @group Service
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Service\GenitivfyService', $this->createTestObject());
    }

    /**
     * @test
     * @group        LandingpageBundle
     * @group        Service
     * @depends      itShouldBeInstantiable
     *
     * @param string $name
     * @param string $locale
     * @param string $expectedName
     *
     * @dataProvider nameProvider
     */
    public function itShouldGenitivfyNames($name, $locale, $expectedName)
    {
        $this->assertEquals($expectedName, $this->createTestObject()->genitivfy($name, new IdentValue($locale)));
    }

    /**
     * @return array
     */
    public function nameProvider()
    {
        return [
            ["Carolin", "en", "Carolin's"],
            ["Klaus", "en", "Klaus'"],
            ["Weiß", "en", "Weiß's"],
            ["Katz", "en", "Katz's"],
            ["Merz", "en", "Merz's"],
            ["Marx", "en", "Marx's"],

            ["Carolin", "de", "Carolins"],
            ["Klaus", "de", "Klaus'"],
            ["Weiß", "de", "Weiß'"],
            ["Katz", "de", "Katz'"],
            ["Merz", "de", "Merz'"],
            ["Marx", "de", "Marx'"],

            ["Carolin", "fr", "Carolin"],
            ["Klaus", "fr", "Klaus"],
            ["Weiß", "fr", "Weiß"],
            ["Katz", "fr", "Katz"],
            ["Merz", "fr", "Merz"],
            ["Marx", "fr", "Marx"],

            ["Carolin", "es", "Carolin"],
            ["Klaus", "es", "Klaus"],
            ["Weiß", "es", "Weiß"],
            ["Katz", "es", "Katz"],
            ["Merz", "es", "Merz"],
            ["Marx", "es", "Marx"],
        ];
    }

    /**
     * @return GenitivfyService
     */
    protected function createTestObject()
    {
        return new GenitivfyService();
    }
}
