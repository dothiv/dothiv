<?php

namespace Dothiv\HivDomainStatusBundle\Tests\Entity\Command;

use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\HivDomainStatusBundle\Command\FetchHivDomainStatusCommand;
use Dothiv\HivDomainStatusBundle\Service\HivDomainStatusServiceInterface;
use Dothiv\ValueObject\URLValue;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FetchHivDomainStatusCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var \Doctrine\ORM\EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConfigRepo;

    /**
     * @test
     * @group HivDomainStatusBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\HivDomainStatusBundle\Command\FetchHivDomainStatusCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   HivDomainStatusBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldFetchChecks()
    {
        $containerMap = array(
            array('dothiv_hiv_domain_status.service', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockService),
            array('dothiv.repository.config', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockConfigRepo),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $config = new Config();
        $config->setName('dothiv_hiv_domain_status.check.next_url');
        $this->mockConfigRepo->expects($this->once())->method('get')
            ->with('dothiv_hiv_domain_status.check.next_url')
            ->will($this->returnValue($config));

        $this->mockConfigRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (Config $config) {
                $this->assertEquals('http://localhost:8889/check?offsetKey=2', $config->getValue());
                return true;
            }))
            ->will($this->returnSelf());
        $this->mockConfigRepo->expects($this->once())->method('flush');

        $this->mockService->expects($this->once())->method('fetchChecks')
            ->with(null)
            ->will($this->returnValue('http://localhost:8889/check?offsetKey=2'));
        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return FetchHivDomainStatusCommand
     */
    protected function getTestObject()
    {
        $command = new FetchHivDomainStatusCommand();
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

        $this->mockConfigRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
