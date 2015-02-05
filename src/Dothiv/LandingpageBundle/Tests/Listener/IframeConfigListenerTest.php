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

class IframeConfigListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var LandingpageConfigurationRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConfigRepo;

    /**
     * @var ContentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContent;

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

        $this->mockConfigRepo->expects($this->once())->method('findByDomain')
            ->with($domain)
            ->willReturn(Option::fromValue($landingpageConfig));

        $this->mockContent->expects($this->any())->method('buildEntry')
            ->with('String')
            ->willReturn((object)['value' => 'some string']);

        $event = new ClickCounterConfigurationEvent($domain, $config);
        $this->createTestObject()->onClickCounterConfiguration($event);
        $this->assertArrayHasKey('landingPage', $event->getConfig());
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

        $this->mockConfigRepo->expects($this->once())->method('findByDomain')
            ->with($domain)
            ->willReturn(None::create());

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
            $this->mockConfigRepo,
            $this->mockContent,
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
        $this->mockConfigRepo = $this->getMock('\Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface');
        $this->mockContent    = $this->getMock('\Dothiv\BaseWebsiteBundle\Contentful\ContentInterface');
    }
}
