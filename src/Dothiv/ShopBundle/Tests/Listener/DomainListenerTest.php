<?php


namespace Dothiv\ShopBundle\Test\Listener;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\ShopBundle\Entity\DomainInfo;
use Dothiv\BusinessBundle\Event\DomainEvent;
use Dothiv\ShopBundle\Repository\DomainInfoRepositoryInterface;
use Dothiv\ShopBundle\Listener\DomainListener;
use Dothiv\ValueObject\HivDomainValue;

class DomainListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DomainInfoRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainInfoRepo;

    /**
     * @test
     * @group Shop
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\ShopBundle\Listener\DomainListener', $this->createTestObject());
    }

    /**
     * @test
     * @group   Shop
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldRegisterADomain()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');
        $event = new DomainEvent($domain);

        $info = new DomainInfo();
        $info->setName(new HivDomainValue('example.hiv'));
        $info->setRegistered(false);

        $this->mockDomainInfoRepo->expects($this->once())->method('getByDomain')
            ->with($info->getName())
            ->willReturn($info);

        $this->mockDomainInfoRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (DomainInfo $info) {
                $this->assertEquals('example.hiv', $info->getName()->toScalar());
                $this->assertTrue($info->getRegistered());
                return true;
            }))
            ->willReturnSelf();
        $this->mockDomainInfoRepo->expects($this->once())->method('flush')->willReturnSelf();

        $this->createTestObject()->onDomainRegistered($event);
    }

    /**
     * @test
     * @group   Shop
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldUnregisterADomain()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');
        $event = new DomainEvent($domain);

        $info = new DomainInfo();
        $info->setName(new HivDomainValue('example.hiv'));
        $info->setRegistered(true);

        $this->mockDomainInfoRepo->expects($this->once())->method('getByDomain')
            ->with($info->getName())
            ->willReturn($info);

        $this->mockDomainInfoRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (DomainInfo $info) {
                $this->assertEquals('example.hiv', $info->getName()->toScalar());
                $this->assertFalse($info->getRegistered());
                return true;
            }))
            ->willReturnSelf();
        $this->mockDomainInfoRepo->expects($this->once())->method('flush')->willReturnSelf();

        $this->createTestObject()->onDomainDeleted($event);
    }

    /**
     * @return DomainListener
     */
    protected function createTestObject()
    {
        return new DomainListener($this->mockDomainInfoRepo);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockDomainInfoRepo = $this->getMock('\Dothiv\ShopBundle\Repository\DomainInfoRepositoryInterface');
    }
}
