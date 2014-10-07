<?php

namespace Dothiv\BusinessBundle\Tests\Entity\Command;

use Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Service\ClickCounterConfigInterface;
use Dothiv\ValueObject\ClockValue;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Test for ClickCounterConfigureCommand.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 */
class ClickCounterConfigureCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Symfony\Component\Console\Input\InputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInput;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOutput;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;

    /**
     * @var ClickCounterConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockClickCounterConfig;

    /**
     * @var \Doctrine\ORM\EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockBannerRepo;

    /**
     * @var \Doctrine\ORM\EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConfigRepo;

    /**
     * @test
     * @group DothivBusinessBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldConfigureBanners()
    {
        $domain = new Domain();
        $domain->setName('stop.hiv');
        $banner = new Banner();
        $banner->setDomain($domain);

        $containerMap = array(
            array('clickcounter', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockClickCounterConfig),
            array('dothiv.repository.banner', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockBannerRepo),
            array('dothiv.repository.config', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockConfigRepo),
            array('clock', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->getClock()),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $config = new Config();
        $config->setName('clickcounter_config.last_run');
        $this->mockConfigRepo->expects($this->once())->method('get')
            ->with('clickcounter_config.last_run')
            ->will($this->returnValue($config));
        $this->mockConfigRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (Config $config) {
                $this->assertEquals('2014-01-02T13:14:15+00:00', $config->getValue());
                return true;
            }))
            ->will($this->returnSelf());
        $this->mockConfigRepo->expects($this->once())->method('flush');

        $this->mockBannerRepo->expects($this->once())->method('findAll')
            ->will($this->returnValue(array($banner)));

        $this->mockClickCounterConfig->expects($this->once())->method('setup')
            ->with($banner);

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return ClickCounterConfigureCommand
     */
    protected function getTestObject()
    {
        $command = new ClickCounterConfigureCommand();
        $command->setContainer($this->mockContainer);
        return $command;
    }

    /**
     * @return ClockValue
     */
    protected function getClock()
    {
        $clock = new ClockValue(new \DateTime('2014-01-02T13:14:15Z'));
        return $clock;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockInput = $this->getMockBuilder('\Symfony\Component\Console\Input\InputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockOutput = $this->getMockBuilder('\Symfony\Component\Console\Output\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockContainer = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockClickCounterConfig = $this->getMockBuilder('\Dothiv\BusinessBundle\Service\ClickCounterConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockBannerRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\BannerRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockConfigRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
} 
