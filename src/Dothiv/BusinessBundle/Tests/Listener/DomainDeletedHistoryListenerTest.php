<?php


namespace Dothiv\BusinessBundle\Tests\Listener;

use Dothiv\BusinessBundle\Entity\DeletedDomain;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\BusinessBundle\Listener\DeletedDomainHistoryListener;
use Dothiv\BusinessBundle\Repository\DeletedDomainRepositoryInterface;

class DeletedDomainHistoryListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DeletedDomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDeletedDomainRepo;

    /**
     * @test
     * @group DothivBusinessBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Listener\DeletedDomainHistoryListener', $this->getTestObject());
    }

    /**
     * @test
     * @group DothivBusinessBundle
     * @group Listener
     */
    public function itShouldStoreADeletedDomainEntry()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');

        $this->mockDeletedDomainRepo->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (DeletedDomain $d) {
                $this->assertEquals('example.hiv', $d->getDomain()->toScalar());
                return true;
            }))
            ->willReturnSelf();

        $this->mockDeletedDomainRepo->expects($this->once())
            ->method('flush')
            ->willReturnSelf();

        $event = new DomainEvent($domain);

        $this->getTestObject()->onDomainDeleted($event);
    }

    /**
     * @return DeletedDomainHistoryListener
     */
    protected function getTestObject()
    {
        return new DeletedDomainHistoryListener($this->mockDeletedDomainRepo);
    }

    public function setUp()
    {
        parent::setUp();

        $this->mockDeletedDomainRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\DeletedDomainRepositoryInterface');
    }
}
