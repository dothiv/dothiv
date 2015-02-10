<?php


namespace Dothiv\LandingpageBundle\Test\Listener;

use Dothiv\BaseWebsiteBundle\Contentful\ContentInterface;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Listener\LandingpageConfigChangedListener;
use Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface;
use Dothiv\LandingpageBundle\Service\GenitivfyService;
use Dothiv\ValueObject\ClockValue;
use PhpOption\None;
use PhpOption\Option;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class LandingpageConfigChangedListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ObjectProphecy
     */
    private $mockBannerRepo;

    /**
     * @var ClockValue
     */
    private $clock;

    /**
     * @test
     * @group LandingpageBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Listener\LandingpageConfigChangedListener', $this->createTestObject());
    }

    /**
     * @test
     * @group   LandingpageBundle
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldCreateLandingPageConfig()
    {
        $banner = new Banner();
        $domain = new Domain();
        $domain->setName('caro4life.hiv');
        $domain->setActiveBanner($banner);
        $landingpageConfig = new LandingpageConfiguration();
        $landingpageConfig->setName('Caro');
        $landingpageConfig->setDomain($domain);

        $this->mockBannerRepo->persist(Argument::that(function (Banner $banner) {
            $this->assertEquals($this->clock->getNow(), $banner->getUpdated());
            return true;
        }))
            ->willReturn($this->mockBannerRepo)
            ->shouldBeCalledTimes(1);

        $this->mockBannerRepo->flush()
            ->willReturn($this->mockBannerRepo)
            ->shouldBeCalledTimes(1);

        $event = new EntityChangeEvent(new EntityChange(), $landingpageConfig);
        $this->createTestObject()->onEntityChanged($event);
    }

    /**
     * @return LandingpageConfigChangedListener
     */
    protected function createTestObject()
    {
        return new LandingpageConfigChangedListener(
            $this->mockBannerRepo->reveal(),
            $this->clock
        );
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockBannerRepo = $this->prophesize('\Dothiv\BusinessBundle\Repository\BannerRepositoryInterface');
        $this->clock          = new ClockValue(new \DateTime('2000-01-01 12:34:56'));
    }

    /**
     * Test setup
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->mockBannerRepo->checkProphecyMethodsPredictions();
    }
}
