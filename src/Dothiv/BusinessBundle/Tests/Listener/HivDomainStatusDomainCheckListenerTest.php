<?php


namespace Dothiv\BusinessBundle\Test\Listener;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Listener\HivDomainStatusDomainCheckListener;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\HivDomainStatusBundle\Event\DomainCheckEvent;
use Dothiv\HivDomainStatusBundle\Model\DomainCheckModel;
use PhpOption\Option;

class HivDomainStatusDomainCheckListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @var EntityChangeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockChangeRepo;

    /**
     * @test
     * @group BusinessBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Listener\HivDomainStatusDomainCheckListener', $this->createTestObject());
    }

    /**
     * @test
     * @group        BusinessBundle
     * @group        Listener
     * @depends      itShouldBeInstantiable
     * @dataProvider domainStatusProvider
     *
     * @param bool $oldValue
     * @param bool $newValue
     */
    public function itShouldUpdateTheDomainStatus($oldValue, $newValue)
    {
        // FIXME: Implement
        $domain = new Domain();
        $domain->setName('example.hiv');
        $domain->setLive($oldValue);

        $this->mockDomainRepo->expects($this->once())->method('getDomainByName')
            ->with('example.hiv')
            ->willReturn(Option::fromValue($domain));
        if ($oldValue !== $newValue) {
            $this->mockDomainRepo->expects($this->once())->method('persist')
                ->with($this->callback(function (Domain $domain) use ($newValue) {
                    $this->assertEquals($newValue, $domain->getLive());
                    return true;
                }))
                ->willReturnSelf();
            $this->mockDomainRepo->expects($this->once())->method('flush');

            $this->mockChangeRepo->expects($this->once())->method('persist')
                ->with($this->callback(function (EntityChange $change) use ($oldValue, $newValue) {
                    /** @var EntityPropertyChange $propChange */
                    $propChange = $change->getChanges()->get('live');
                    $this->assertEquals('live', $propChange->getProperty()->toScalar());
                    $this->assertEquals($oldValue, $propChange->getOldValue());
                    $this->assertEquals($newValue, $propChange->getNewValue());
                    return true;
                }))
                ->willReturnSelf();
            $this->mockChangeRepo->expects($this->once())->method('flush');
        } else {
            $this->mockDomainRepo->expects($this->never())->method('persist');
            $this->mockChangeRepo->expects($this->never())->method('persist');
        }

        $check         = new DomainCheckModel();
        $check->domain = 'example.hiv';
        $check->valid  = $newValue;
        $event         = new DomainCheckEvent($check);
        $this->createTestObject()->onDomainCheck($event);
    }

    /**
     * @return array
     */
    public function domainStatusProvider()
    {
        return array(
            array(false, true),
            array(true, false),
            array(false, false),
            array(true, true),
        );
    }

    /**
     * @return HivDomainStatusDomainCheckListener
     */
    protected function createTestObject()
    {
        return new HivDomainStatusDomainCheckListener($this->mockDomainRepo, $this->mockChangeRepo);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockDomainRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
        $this->mockChangeRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface');
    }
}
