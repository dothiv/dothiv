<?php

namespace Dothiv\BusinessBundle\Tests\Entity\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand;
use Dothiv\BusinessBundle\Command\FetchDomainWhoisCommand;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainWhois;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainWhoisRepositoryInterface;
use Dothiv\BusinessBundle\Service\WhoisServiceInterface;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\None;
use PhpOption\Option;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Test for FetchDomainFetchDomainWhoisCommand.
 */
class FetchDomainFetchDomainWhoisCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var WhoisServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockWhoisService;

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @var DomainWhoisRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainWhoisRepo;

    /**
     * @test
     * @group DothivBusinessBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\BusinessBundle\Command\FetchDomainWhoisCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group        DothivBusinessBundle
     * @group        Command
     * @depends      itShouldBeInstantiateable
     * @dataProvider domainDataProvider
     *
     * @param Domain           $domain
     * @param DomainWhois|null $domainWhois
     */
    public function itShouldFetchWhoisForAllDomains(Domain $domain, $domainWhois = null)
    {
        $containerMap = [
            ['whois', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockWhoisService],
            ['dothiv.repository.domain', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockDomainRepo],
            ['dothiv.repository.domain_whois', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockDomainWhoisRepo],
        ];
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $this->mockDomainRepo->expects($this->once())->method('findAll')
            ->willReturn([$domain]);

        $this->mockDomainWhoisRepo->expects($this->any())->method('findByDomain')
            ->willReturn(Option::fromValue($domainWhois));

        $this->mockDomainWhoisRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (DomainWhois $domainWhois) use ($domain) {
                $this->assertEquals(new HivDomainValue($domain->getName()), $domainWhois->getDomain());
                $this->assertEquals('TLD.HIV', $domainWhois->getWhois()->get('Domain Name'));
                return true;
            }))
            ->willReturnSelf();

        $this->mockDomainWhoisRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $this->mockWhoisService->expects($this->once())->method('lookup')
            ->with($this->callback(function (HivDomainValue $domainName) use ($domain) {
                $this->assertEquals($domain->getName(), $domainName->toScalar());
                return true;
            }))
            ->willReturn(file_get_contents(__DIR__ . '/../Service/data/tld.hiv.whois'));

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    public function domainDataProvider()
    {
        $domain1 = new Domain();
        $domain1->setName('example.hiv');
        $domain2 = new Domain();
        $domain2->setName('example2.hiv');
        return [
            [$domain1, null],
            [$domain2, DomainWhois::create(new HivDomainValue($domain2->getName()), new ArrayCollection())]
        ];
    }

    /**
     * @return ClickCounterConfigureCommand
     */
    protected function getTestObject()
    {
        $command = new FetchDomainWhoisCommand();
        $command->setContainer($this->mockContainer);
        return $command;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockInput           = $this->getMock('\Symfony\Component\Console\Input\InputInterface');
        $this->mockOutput          = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->mockContainer       = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $this->mockWhoisService    = $this->getMock('\Dothiv\BusinessBundle\Service\WhoisServiceInterface');
        $this->mockDomainRepo      = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
        $this->mockDomainWhoisRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainWhoisRepositoryInterface');
    }
}
