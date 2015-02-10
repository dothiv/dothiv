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
     * @var string[]
     */
    private $locales = ['en', 'de', 'es', 'fr'];

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
     * @param string|null $customText
     *
     * @test
     * @group        LandingpageBundle
     * @group        Listener
     * @depends      itShouldBeInstantiable
     * @dataProvider getTestData
     */
    public function itShouldCreateLandingPageConfig($customText = null)
    {
        $domain = new Domain();
        $domain->setName('caro4life.hiv');
        $landingpageConfig = new LandingpageConfiguration();
        $landingpageConfig->setName('Caro');
        $landingpageConfig->setDomain($domain);
        $landingpageConfig->setText($customText);

        $this->mockContent->buildEntry('String', Argument::any(), Argument::any())
            ->willReturn((object)['value' => 'some string'])
            ->shouldBeCalled();

        $config = $this->createTestObject()->buildConfig($landingpageConfig);
        $this->assertEquals('en', $config['defaultLocale']);
        $this->assertArrayHasKey('strings', $config);
        $this->assertCount(4, $config['strings']);
        if (!is_null($customText)) {
            foreach ($this->locales as $locale) {
                $this->assertEquals($customText, $config['strings'][$locale]['about']);
            }
        }
    }

    /**
     * @return array
     */
    public function getTestData()
    {
        return [
            [null],
            ['This is my text.']
        ];
    }

    /**
     * @return LandingpageConfigService
     */
    protected function createTestObject()
    {
        return new LandingpageConfigService(
            $this->mockContent->reveal(),
            ['locales' => $this->locales],
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
