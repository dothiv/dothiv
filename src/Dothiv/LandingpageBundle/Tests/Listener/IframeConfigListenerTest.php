<?php


namespace Dothiv\LandingpageBundle\Test\Listener;

use Dothiv\BaseWebsiteBundle\Contentful\ContentInterface;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Listener\IframeConfigListener;
use Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface;
use Dothiv\LandingpageBundle\Service\GenitivfyService;
use PhpOption\None;
use PhpOption\Option;
use Prophecy\Prophecy\ObjectProphecy;

class IframeConfigListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ObjectProphecy
     */
    private $mockConfigRepo;

    /**
     * @var ObjectProphecy
     */
    private $mockConfigService;

    /**
     * @test
     * @group LandingpageBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Listener\IframeConfigListener', $this->createTestObject());
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
        $config = [];

        $this->mockConfigRepo->findByDomain($domain)
            ->willReturn(Option::fromValue($landingpageConfig))
            ->shouldBeCalledTimes(1);

        $lpConfig = ['some' => 'config'];

        $this->mockConfigService->buildConfig($landingpageConfig)
            ->willReturn($lpConfig)
            ->shouldBeCalledTimes(1);

        $event = new ClickCounterConfigurationEvent($domain, $config);
        $this->createTestObject()->onClickCounterConfiguration($event);
        $this->assertEquals($lpConfig, $event->getConfig()['landingPage']);
    }

    /**
     * @test
     * @group   LandingpageBundle
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldNotCreateLandingPageConfigForRegularDomains()
    {
        $domain = new Domain();
        $domain->setName('caro.hiv');

        $this->mockConfigRepo->findByDomain($domain)
            ->willReturn(None::create())
            ->shouldBeCalledTimes(1);

        $event = new ClickCounterConfigurationEvent($domain, []);
        $this->createTestObject()->onClickCounterConfiguration($event);
        $this->assertArrayNotHasKey('landingPage', $event->getConfig());
    }

    /**
     * @return IframeConfigListener
     */
    protected function createTestObject()
    {
        return new IframeConfigListener(
            $this->mockConfigRepo->reveal(),
            $this->mockConfigService->reveal()
        );
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockConfigRepo    = $this->prophesize('\Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface');
        $this->mockConfigService = $this->prophesize('\Dothiv\LandingpageBundle\Service\LandingpageConfigServiceInterface');
    }
}
