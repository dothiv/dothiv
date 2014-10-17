<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Command;

use Dothiv\CharityWebsiteBundle\Command\SendClickCounterConfigurationCommand;
use Dothiv\CharityWebsiteBundle\Service\SendClickCounterConfigurationServiceInterface;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SendClickCounterConfigurationCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInput;

    /**
     * @var OutputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOutput;

    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;

    /**
     * @var SendClickCounterConfigurationServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockService;

    /**
     * @test
     * @group CharityWebsiteBundle
     * @group Command
     */
    public function itShouldBeInstantiatable()
    {
        $this->assertInstanceOf('\Dothiv\CharityWebsiteBundle\Command\SendClickCounterConfigurationCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   CharityWebsiteBundle
     * @group   Command
     * @depends itShouldBeInstantiatable
     */
    public function itShouldCallNotify()
    {
        $containerMap = array(
            array('dothiv.charity.clickcounter_notification', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockService)
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));
        $this->mockContainer->expects($this->any())->method('getParameter')
            ->with('kernel.environment')->willReturn('test');

        // Get domain from input
        $this->mockInput->expects($this->once())->method('getArgument')->with('domain')
            ->willReturn('example.hiv');

        // Send email
        $this->mockService->expects($this->once())->method('sendConfiguration')
            ->with(new HivDomainValue('example.hiv'));

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return SendClickCounterConfigurationCommand
     */
    protected function getTestObject()
    {
        $command = new SendClickCounterConfigurationCommand();
        $command->setContainer($this->mockContainer);
        return $command;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockInput
            = $this->getMock('\Symfony\Component\Console\Input\InputInterface');
        $this->mockOutput
            = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->mockContainer
            = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $this->mockService
            = $this->getMock('\Dothiv\CharityWebsiteBundle\Service\SendClickCounterConfigurationServiceInterface');

    }
} 
