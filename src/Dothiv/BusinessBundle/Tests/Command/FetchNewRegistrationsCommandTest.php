<?php

namespace Dothiv\BusinessBundle\Tests\Entity\Command;

use Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand;
use Dothiv\BusinessBundle\Command\FetchNewRegistrationsCommand;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\ValueObject\URLValue;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Test for FetchNewRegistrationsCommand
 *
 * @author Markus Tacker <m@click4life.hiv>
 */
class FetchNewRegistrationsCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var \Dothiv\AfiliasImporterBundle\Service\AfiliasImporterServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockAfiliasImporterService;

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
        $this->assertInstanceOf('Dothiv\BusinessBundle\Command\FetchNewRegistrationsCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldFetchRegistrations()
    {
        $containerMap = array(
            array('dothiv_afilias_importer.service', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockAfiliasImporterService),
            array('dothiv.repository.config', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockConfigRepo),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $parameterMap = array(
            array('dothiv_afilias_importer.service_url', 'http://localhost:8666/'),
            array('kernel.environment', 'test'),
        );
        $this->mockContainer->expects($this->any())->method('getParameter')
            ->will($this->returnValueMap($parameterMap));

        $this->mockAfiliasImporterService->expects($this->once())->method('fetchRegistrations')
            ->with($this->callback(function (URLValue $url) {
                $this->assertEquals('http://localhost:8666/registrations', (string)$url);
                return true;
            }))
            ->will($this->returnValue('http://localhost:8666/registrations?offsetKey=2863499'));

        $config = new Config();
        $config->setName('dothiv_afilias_importer.next_url');
        $this->mockConfigRepo->expects($this->once())->method('get')
            ->with('dothiv_afilias_importer.next_url')
            ->will($this->returnValue($config));

        $this->mockConfigRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (Config $config) {
                $this->assertEquals('http://localhost:8666/registrations?offsetKey=2863499', $config->getValue());
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
        $command = new FetchNewRegistrationsCommand();
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

        $this->mockAfiliasImporterService = $this->getMockBuilder('\Dothiv\AfiliasImporterBundle\Service\AfiliasImporterServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockConfigRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
