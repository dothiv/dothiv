<?php

namespace Dothiv\BusinessBundle\Tests\Entity\Command;

use Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand;
use Dothiv\BusinessBundle\Command\UserCreateCommand;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Test for UserCreateCommand.
 */
class UserCreateCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var UserServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserService;

    /**
     * @test
     * @group DothivBusinessBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\BusinessBundle\Command\UserCreateCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldListAllConfigSettings()
    {
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap([
                ['dothiv.businessbundle.service.user', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockUserService]
            ]));
        $this->mockInput->expects($this->any())->method('getArgument')
            ->will($this->returnValueMap([
                ['email', 'john.doe@example.com'],
                ['firstname', 'John'],
                ['lastname', 'Doe'],
            ]));
        $this->mockUserService->expects($this->once())->method('getOrCreateUser')
            ->with('john.doe@example.com', 'John', 'Doe')
            ->willReturn(new User());

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return ClickCounterConfigureCommand
     */
    protected function getTestObject()
    {
        $command = new UserCreateCommand();
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

        $this->mockUserService = $this->getMock('\Dothiv\BusinessBundle\Service\UserServiceInterface');
    }
}
