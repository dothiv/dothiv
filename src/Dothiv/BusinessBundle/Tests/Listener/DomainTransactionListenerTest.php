<?php


namespace Dothiv\BusinessBundle\Tests\Listener;

use Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Listener\DomainTransactionListener;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Service\IRegistration;
use PhpOption\Option;

class DomainTransactionListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var IRegistration|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRegistration;

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @test
     * @group DothivBusinessBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Listener\DomainTransactionListener', $this->getTestObject());
    }

    /**
     * @test
     * @group DothivBusinessBundle
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
     * @return DomainTransactionListener
     */
    protected function getTestObject()
    {
        return new DomainTransactionListener($this->mockRegistration, $this->mockDomainRepo);
    }

    public function setUp()
    {
        parent::setUp();

        $this->mockRegistration = $this->getMockBuilder('\Dothiv\BusinessBundle\Service\IRegistration')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockDomainRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
