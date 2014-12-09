<?php

namespace Dothiv\HivDomainStatusBundle\Tests\Entity\Command;

use Dothiv\HivDomainStatusBundle\Command\FetchDomainsCommand;
use Dothiv\HivDomainStatusBundle\Service\HivDomainStatusServiceInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FetchDomainsCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var HivDomainStatusServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockService;

    /**
     * @test
     * @group HivDomainStatusBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\HivDomainStatusBundle\Command\FetchDomainsCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   HivDomainStatusBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldFetchRegistrations()
    {
        $containerMap = array(
            array('dothiv_hiv_domain_status.service', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockService),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $this->mockService->expects($this->once())->method('fetchDomains');
        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return FetchDomainsCommand
     */
    protected function getTestObject()
    {
        $command = new FetchDomainsCommand();
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

        $this->mockService = $this->getMockBuilder('\Dothiv\HivDomainStatusBundle\Service\HivDomainStatusServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
