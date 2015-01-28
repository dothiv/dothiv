<?php

namespace Dothiv\BusinessBundle\Tests\Entity\Command;

use Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand;
use Dothiv\BusinessBundle\Command\DomainOwnerSetCommand;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use PhpOption\Option;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Test for DomainOwnerSetCommand.
 */
class DomainOwnerSetCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var UserRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserRepo;

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @test
     * @group DothivBusinessBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\BusinessBundle\Command\DomainOwnerSetCommand', $this->getTestObject());
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
                ['dothiv.repository.user', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockUserRepo],
                ['dothiv.repository.domain', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockDomainRepo]
            ]));
        $this->mockInput->expects($this->any())->method('getArgument')
            ->will($this->returnValueMap([
                ['email', 'john.doe@example.com'],
                ['domain', 'example.hiv']
            ]));

        $user = new User();
        $this->mockUserRepo->expects($this->once())->method('getUserByEmail')
            ->with('john.doe@example.com')
            ->willReturn(Option::fromValue($user));

        $domain = new Domain();
        $this->mockDomainRepo->expects($this->once())->method('getDomainByName')
            ->with('example.hiv')
            ->willReturn(Option::fromValue($domain));

        $this->mockDomainRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (Domain $domain) use ($user) {
                $this->assertEquals($user, $domain->getOwner());
                return true;
            }))
            ->willReturnSelf();
        $this->mockDomainRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));

    }

    /**
     * @return ClickCounterConfigureCommand
     */
    protected function getTestObject()
    {
        $command = new DomainOwnerSetCommand();
        $command->setContainer($this->mockContainer);
        return $command;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockInput      = $this->getMock('\Symfony\Component\Console\Input\InputInterface');
        $this->mockOutput     = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->mockContainer  = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $this->mockUserRepo   = $this->getMock('\Dothiv\BusinessBundle\Repository\UserRepositoryInterface');
        $this->mockDomainRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
    }
}
