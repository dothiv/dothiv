<?php

namespace Dothiv\UserReminderBundle\Tests\Command;

use Dothiv\UserReminderBundle\Command\SendRemindersCommand;
use Dothiv\UserReminderBundle\Service\UserReminderRegistryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SendRemindersCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var UserReminderRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserReminderRegistry;

    /**
     * @test
     * @group UserReminderBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\UserReminderBundle\Command\SendRemindersCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   UserReminderBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldCallService()
    {
        $containerMap = array(
            array('dothiv.userreminder.registry', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockUserReminderRegistry)
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $parameterMap = array(
            array('kernel.environment', 'test')
        );
        $this->mockContainer->expects($this->any())->method('getParameter')
            ->will($this->returnValueMap($parameterMap));

        $this->mockUserReminderRegistry->expects($this->once())->method('send');

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return SendRemindersCommand
     */
    protected function getTestObject()
    {
        $command = new SendRemindersCommand();
        $command->setContainer($this->mockContainer);
        return $command;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockInput                = $this->getMock('\Symfony\Component\Console\Input\InputInterface');
        $this->mockOutput               = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->mockContainer            = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $this->mockUserReminderRegistry = $this->getMock('\Dothiv\UserReminderBundle\Service\UserReminderRegistryInterface');
    }
}
