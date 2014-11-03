<?php


namespace Dothiv\BusinessBundle\Tests\Listener;

use Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Listener\DomainTransactionListener;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\RegistrarRepositoryInterface;
use Dothiv\BusinessBundle\Service\IRegistration;
use Dothiv\BusinessBundle\Service\WhoisServiceInterface;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\None;
use PhpOption\Option;

class DomainTransactionListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var IRegistration|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRegistration;

    /**
     * @var RegistrarRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRegistrarRepo;

    /**
     * @var WhoisServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockWhoisService;

    /**
     * @test
     * @group DothivBusinessBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Listener\DomainTransactionListener', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldDeleteDomains()
    {
        $name = 'example.hiv';

        $this->mockRegistration->expects($this->once())
            ->method('deleted')
            ->with($name);

        $this->mockDomainRepo->expects($this->once())
            ->method('getDomainByName')
            ->with($name)
            ->willReturn(Option::fromValue(new Domain()));

        $event             = new DomainTransactionEvent();
        $event->ObjectName = $name;

        $this->getTestObject()->onDomainDeleted($event);
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldMarkDomainsAsInTransfer()
    {
        $name = 'example.hiv';

        $this->mockDomainRepo->expects($this->once())
            ->method('getDomainByName')
            ->with($name)
            ->willReturn(Option::fromValue(new Domain()));

        $this->mockDomainRepo->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Domain $domain) {
                $this->assertTrue($domain->getTransfer());
                return true;
            }))
            ->willReturnSelf();
        $this->mockDomainRepo->expects($this->once())->method('flush');

        $event             = new DomainTransactionEvent();
        $event->ObjectName = $name;

        $this->getTestObject()->onDomainTransferred($event);
    }

    /**
     * @test
     * @group   DothivBusinessBundle
     * @group   Listener
     * @depends itShouldMarkDomainsAsInTransfer
     */
    public function itShouldCreateDomainsForTransfersIfNotExist()
    {
        $name           = 'example.hiv';
        $registrarExtId = '1234-AB';

        $this->mockDomainRepo->expects($this->once())
            ->method('getDomainByName')
            ->with($name)
            ->willReturn(None::create());

        $this->mockRegistrarRepo->expects($this->once())
            ->method('getByExtId')
            ->with($registrarExtId)
            ->willReturn(new Registrar());

        $this->mockWhoisService->expects($this->once())->method('lookup')
            ->with($this->callback(function (HivDomainValue $domain) {
                $this->assertEquals('example.hiv', $domain->toScalar());
                return true;
            }))
            ->willReturnCallback(function () use ($name) {
                return <<<EOF
Domain Name:EXAMPLE.HIV 
Registrant Name:John Doe 
Registrant Email:john.doe@example.com 
EOF;
            });

        $this->mockDomainRepo->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Domain $domain) use ($name, $registrarExtId) {
                $this->assertEquals($name, $domain->getName());
                $this->assertTrue($domain->getTransfer());
                $this->assertEquals("John Doe", $domain->getOwnerName());
                $this->assertEquals("john.doe@example.com", $domain->getOwnerEmail());
                return true;
            }))
            ->willReturnSelf();
        $this->mockDomainRepo->expects($this->once())->method('flush');

        $event                 = new DomainTransactionEvent();
        $event->ObjectName     = $name;
        $event->RegistrarExtID = $registrarExtId;

        $this->getTestObject()->onDomainTransferred($event);
    }

    /**
     * @return DomainTransactionListener
     */
    protected function getTestObject()
    {
        return new DomainTransactionListener(
            $this->mockRegistration,
            $this->mockDomainRepo,
            $this->mockRegistrarRepo,
            $this->mockWhoisService);
    }

    public function setUp()
    {
        parent::setUp();

        $this->mockRegistration  = $this->getMock('\Dothiv\BusinessBundle\Service\IRegistration');
        $this->mockDomainRepo    = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
        $this->mockRegistrarRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\RegistrarRepositoryInterface');
        $this->mockWhoisService  = $this->getMock('\Dothiv\BusinessBundle\Service\WhoisServiceInterface');
    }
}
