<?php

namespace Dothiv\BusinessBundle\Tests\Listener;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserNotification;
use Dothiv\BusinessBundle\Event\UserEvent;
use Dothiv\BusinessBundle\Listener\CreateUserNotificationEmailChangeListener;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserNotificationRepositoryInterface;
use Dothiv\ValueObject\EmailValue;

class CreateUserNotificationEmailChangeListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @var UserNotificationRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockuserNotificationRepo;

    /**
     * @test
     * @group DothivBusinessBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Listener\CreateUserNotificationEmailChangeListener', $this->getTestObject());
    }

    /**
     * @test
     * @group DothivBusinessBundle
     */
    public function itShouldCreateANotification()
    {
        $user = new User();
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $user->setEmail('john@example.com');

        $domain = new Domain();

        $this->mockDomainRepo->expects($this->once())
            ->method('findByOwnerEmail')
            ->with(new EmailValue('john@example.com'))
            ->willReturn(new ArrayCollection(array($domain)));

        $this->mockuserNotificationRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (UserNotification $n) use ($user) {
                $this->assertEquals(array('role' => 'charity.change_email'), $n->getProperties());
                $this->assertEquals($user, $n->getUser());
                return true;
            }))
            ->willReturnSelf();
        $this->mockuserNotificationRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $event = new UserEvent($user);
        $this->getTestObject()->onUserCreated($event);
    }

    /**
     * @return CreateUserNotificationEmailChangeListener
     */
    protected function getTestObject()
    {
        return new CreateUserNotificationEmailChangeListener($this->mockDomainRepo, $this->mockuserNotificationRepo);
    }

    public function setUp()
    {
        $this->mockDomainRepo           = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
        $this->mockuserNotificationRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\UserNotificationRepositoryInterface');
    }
}
