<?php


namespace Dothiv\LandingpageBundle\Test\Service;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Service\GenitivfyService;
use Dothiv\LandingpageBundle\Service\LandingpageConfigService;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Argument;

class LandingpageConfigServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ObjectProphecy
     */
    private $mockContent;

    /**
     * @test
     * @group LandingpageBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Service\LandingpageConfigService', $this->createTestObject());
    }

    /**
     * @test
     * @group   LandingpageBundle
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldCreateLandingPageConfig()
    {
        $domain = new Domain();
        $domain->setName('caro4life.hiv');
        $landingpageConfig = new LandingpageConfiguration();
        $landingpageConfig->setName('Caro');
        $landingpageConfig->setDomain($domain);

        $this->mockContent->buildEntry('String', Argument::any(), Argument::any())
            ->willReturn((object)['value' => 'some string'])
            ->shouldBeCalled();

        $config = $this->createTestObject()->buildConfig($landingpageConfig);
        $this->assertEquals('en', $config['defaultLocale']);
        $this->assertArrayHasKey('strings', $config);
        $this->assertCount(4, $config['strings']);
    }

    /**
     * @return LandingpageConfigService
     */
    protected function createTestObject()
    {
        return new LandingpageConfigService(
            $this->mockContent->reveal(),
            ['locales' => ['en', 'de', 'es', 'fr']],
            new GenitivfyService()
        );
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockContent = $this->prophesize('\Dothiv\BaseWebsiteBundle\Contentful\ContentInterface');
    }
}
