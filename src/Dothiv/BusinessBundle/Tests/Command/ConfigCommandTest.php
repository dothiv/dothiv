<?php

namespace Dothiv\BusinessBundle\Tests\Entity\Command;

use Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand;
use Dothiv\BusinessBundle\Command\ConfigCommand;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Service\ClickCounterConfigInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Test for ConfigCommand.
 */
class ConfigCommandTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('Dothiv\BusinessBundle\Command\ConfigCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldListAllConfigSettings()
    {
        $containerMap = array(
            array('dothiv.repository.config', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockConfigRepo),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $config = new Config();
        $config->setName('key');
        $config->setValue('value');
        $this->mockConfigRepo->expects($this->once())->method('findAll')
            ->willReturn(array($config));

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldListAConfigSettings()
    {
        $containerMap = array(
            array('dothiv.repository.config', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockConfigRepo),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $this->mockInput->expects($this->any())->method('getArgument')
            ->will($this->returnValueMap(array(
                array('name', 'key'),
                array('value', null)
            )));

        $config = new Config();
        $config->setName('key');
        $config->setValue('value');
        $this->mockConfigRepo->expects($this->once())->method('get')
            ->with('key')
            ->willReturn($config);

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldUpdateAConfigSettings()
    {
        $containerMap = array(
            array('dothiv.repository.config', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockConfigRepo),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $this->mockInput->expects($this->any())->method('getArgument')
            ->will($this->returnValueMap(array(
                array('name', 'key'),
                array('value', 'new value')
            )));

        $config = new Config();
        $config->setName('key');
        $config->setValue('value');
        $this->mockConfigRepo->expects($this->any())->method('get')
            ->with('key')
            ->willReturn($config);
        $this->mockConfigRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (Config $config) {
                $this->assertEquals('key', $config->getName());
                $this->assertEquals('new value', $config->getValue());
                return true;
            }))
            ->willReturnSelf();
        $this->mockConfigRepo->expects($this->once())->method('flush');

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return ClickCounterConfigureCommand
     */
    protected function getTestObject()
    {
        $command = new ConfigCommand();
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
        $this->mockOutput->expects($this->any())->method('getFormatter')
            ->willReturn($this->getMock('\Symfony\Component\Console\Formatter\OutputFormatterInterface'));

        $this->mockContainer = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockConfigRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\ConfigRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }
} 
