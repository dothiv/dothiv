<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Command;

use Behat\Behat\Console\Input\InputDefinition;
use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\CharityWebsiteBundle\Command\SendClickCounterConfigurationToUnconfiguredCommand;
use Dothiv\CharityWebsiteBundle\Service\SendClickCounterConfigurationServiceInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SendClickCounterConfigurationToUnconfiguredCommandTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Dothiv\CharityWebsiteBundle\Command\SendClickCounterConfigurationToUnconfiguredCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group CharityWebsiteBundle
     * @group Command
     */
    public function itShouldNotifyUnconfigured()
    {
        $containerMap = array(
            array('dothiv.charity.clickcounter_notification', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockService)
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));
        $this->mockContainer->expects($this->any())->method('getParameter')
            ->with('kernel.environment')->willReturn('test');

        $domain = new Domain();
        $domain->setName('example.hiv');
        $this->mockService->expects($this->once())->method('findDomainsToBeNotified')
            ->willReturn(new ArrayCollection(array(
                $domain
            )));

        $mockSubCommand = $this->getMockBuilder('\Symfony\Component\Console\Command\Command')
            ->disableOriginalConstructor()->getMock();
        $mockSubCommand->expects($this->once())->method('run')
            ->with(
                $this->callback(function (ArrayInput $input) {
                    $this->assertEquals('example.hiv', $input->getParameterOption('domain'));
                    return true;
                }),
                $this->mockOutput
            );

        $mockApp = $this->getMock('\Symfony\Component\Console\Application');
        $mockApp->expects($this->once())->method('find')->with('charity:clickcounter:send-configuration')
            ->willReturn($mockSubCommand);
        $mockApp->expects($this->any())->method('getHelperSet')
            ->willReturn(new HelperSet());
        $mockApp->expects($this->any())->method('getDefinition')
            ->willReturn(new InputDefinition());

        $command = $this->getTestObject();
        $command->setApplication($mockApp);

        $this->assertEquals(0, $command->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return SendClickCounterConfigurationToUnconfiguredCommand
     */
    protected function getTestObject()
    {
        $command = new SendClickCounterConfigurationToUnconfiguredCommand();
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
