<?php

namespace Dothiv\BusinessBundle\Tests\Entity\Command;

use Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand;
use Dothiv\BusinessBundle\Command\ClickCounterStatsCommand;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Service\ClickCounterConfigInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Test for ClickCounterConfigureCommand.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 */
class ClickCounterStatsCommandTest extends \PHPUnit_Framework_TestCase
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
    private $mockConfigRepo;

    /**
     * @test
     * @group DothivBusinessBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\BusinessBundle\Command\ClickCounterStatsCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldFetchStats()
    {
        $containerMap = array(
            array('clickcounter', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockClickCounterConfig),
            array('dothiv.repository.config', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockConfigRepo),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $this->mockClickCounterConfig->expects($this->once())->method('getClickCount')
            ->will($this->returnValue(1234));

        $config = new Config();
        $config->setName('clickcount');
        $this->mockConfigRepo->expects($this->once())->method('get')
            ->with('clickcount')
            ->will($this->returnValue($config));

        $this->mockConfigRepo->expects($this->once())->method('persist')
            ->with($this->callback(function(Config $config) {
                $this->assertEquals(1234, $config->getValue());
                return true;
            }))
            ->will($this->returnSelf());

        $this->mockConfigRepo->expects($this->once())->method('flush');

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return ClickCounterConfigureCommand
     */
    protected function getTestObject()
    {
        $command = new ClickCounterStatsCommand();
        $command->setContainer($this->mockContainer);
        return $command;
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

        $this->mockConfigRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
} 
