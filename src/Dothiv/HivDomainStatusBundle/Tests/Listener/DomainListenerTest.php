<?php


namespace Dothiv\HivDomainStatusBundle\Test\Listener;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\HivDomainStatusBundle\Listener\DomainListener;
use Dothiv\HivDomainStatusBundle\Service\HivDomainStatusServiceInterface;

class DomainListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var HivDomainStatusServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockService;

    /**
     * @test
     * @group HivDomainStatus
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\HivDomainStatusBundle\Listener\DomainListener', $this->createTestObject());
    }

    /**
     * @test
     * @group   HivDomainStatus
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldRegisterADomain()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');
        $event = new DomainEvent($domain);

        $this->mockService->expects($this->once())->method('registerDomain')
            ->with($domain);

        $this->createTestObject()->onDomainRegistered($event);
    }

    /**
     * @test
     * @group   HivDomainStatus
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldUnregisterADomain()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');
        $event = new DomainEvent($domain);

        $this->mockService->expects($this->once())->method('unregisterDomain')
            ->with($domain);

        $this->createTestObject()->onDomainDeleted($event);
    }

    /**
     * @return DomainListener
     */
    protected function createTestObject()
    {
        return new DomainListener($this->mockService);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockService = $this->getMock('\Dothiv\HivDomainStatusBundle\Service\HivDomainStatusServiceInterface');
    }
}
