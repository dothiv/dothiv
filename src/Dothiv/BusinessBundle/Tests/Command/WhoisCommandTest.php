<?php

namespace Dothiv\BusinessBundle\Tests\Entity\Command;

use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand;
use Dothiv\BusinessBundle\Command\WhoisCommand;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Service\WhoisServiceInterface;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Test for WhoisCommand.
 */
class WhoisCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var WhoisServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockWhoisService;

    /**
     * @test
     * @group DothivBusinessBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\BusinessBundle\Command\WhoisCommand', $this->getTestObject());
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
            array('whois', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockWhoisService),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));
        $this->mockInput->expects($this->any())->method('getArgument')
            ->will($this->returnValueMap(array(
                array('domain', 'example.hiv')
            )));
        $this->mockWhoisService->expects($this->once())->method('lookup')
            ->with($this->callback(function (HivDomainValue $domain) {
                $this->assertEquals('example.hiv', $domain->toScalar());
                return true;
            }));
        $this->mockOutput->expects($this->atLeastOnce())->method('writeln');

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return ClickCounterConfigureCommand
     */
    protected function getTestObject()
    {
        $command = new WhoisCommand();
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

        $this->mockWhoisService = $this->getMock('\Dothiv\BusinessBundle\Service\WhoisServiceInterface');
    }
}
