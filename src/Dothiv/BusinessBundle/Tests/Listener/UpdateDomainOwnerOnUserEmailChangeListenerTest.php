<?php

namespace Dothiv\BusinessBundle\Tests\Listener;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\BusinessBundle\Listener\UpdateDomainOwnerOnUserEmailChangeListener;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateDomainOwnerOnUserEmailChangeListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockEventDispatcher;

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @var EntityChangeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockEntityChangeRepo;

    /**
     * @test
     * @group Service
     * @group BusinessBundle
     * @group Registration
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Listener\UpdateDomainOwnerOnUserEmailChangeListener', $this->createTestObject());
    }

    /**
     * @test
     * @group   Service
     * @group   BusinessBundle
     * @group   Registration
     * @depends itShouldBeInstantiable
     */
    public function itShouldActOnChangedUserEmail()
    {
        $user = new User();
        $user->setEmail('new@email.de');
        $user->setHandle('somehandle');
        $change = new EntityChange();
        $change->addChange(new IdentValue('email'), 'old@email.de', 'new@email.de');
        $change->setEntity(get_class($user));
        $change->setIdentifier(new IdentValue('somehandle'));
        $change->setAuthor(new EmailValue('old@email.de'));
        $event = new EntityChangeEvent($change, $user);
        $event->setDispatcher($this->mockEventDispatcher);

        $domain = new Domain();
        $domain->setOwnerEmail('old@email.de');
        $domain->setName('example.hiv');
        $domains = new ArrayCollection(array($domain));
        $this->mockDomainRepo->expects($this->once())->method('findByOwnerEmail')
            ->willReturn($domains);

        $this->mockDomainRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (Domain $domain) {
                $this->assertEquals('example.hiv', $domain->getName());
                $this->assertEquals('new@email.de', $domain->getOwnerEmail());
                return true;
            }))
            ->willReturnSelf();
        $this->mockDomainRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $this->mockEntityChangeRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (EntityChange $change) {
                $this->assertEquals('example.hiv', $change->getIdentifier());
                $this->assertEquals('new@email.de', $change->getChanges()->get('ownerEmail')->getNewValue());
                $this->assertEquals('old@email.de', $change->getChanges()->get('ownerEmail')->getOldValue());
                return true;
            }))
            ->willReturnSelf();
        $this->mockEntityChangeRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $this->mockEventDispatcher->expects($this->once())->method('dispatch')
            ->with(
                BusinessEvents::ENTITY_CHANGED,
                $this->callback(function (EntityChangeEvent $e) use ($domain) {
                    $this->assertEquals($domain, $e->getEntity());
                    return true;
                }));

        $service = $this->createTestObject();
        $service->onEntityChanged($event);
    }

    /**
     * @return UpdateDomainOwnerOnUserEmailChangeListener
     */
    protected function createTestObject()
    {
        $service = new UpdateDomainOwnerOnUserEmailChangeListener($this->mockDomainRepo, $this->mockEntityChangeRepo);
        return $service;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        $this->mockDomainRepo       = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
        $this->mockEntityChangeRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface');
        $this->mockEventDispatcher  = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }
}
