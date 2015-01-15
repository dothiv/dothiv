<?php

namespace Dothiv\UserReminderBundle\Service\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Events\UserReminderEvent;
use Dothiv\UserReminderBundle\Service\UserReminderRegistry;
use Dothiv\UserReminderBundle\UserReminderEvents;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserReminderRegistryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockEventDispatcher;

    /**
     * @test
     * @group   UserReminderBundle
     * @group   UserReminder
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\UserReminderBundle\Service\UserReminderRegistry', $this->createTestObject());
    }

    /**
     * @test
     * @group   UserReminderBundle
     * @group   UserReminder
     * @depends itShouldBeInstantiable
     */
    public function itShouldCallReminders()
    {
        $mockReminder = $this->getMock('\Dothiv\UserReminderBundle\Service\UserReminderInterface');

        $userReminder = new UserReminder();

        $mockReminder->expects($this->once())->method('send')
            ->willReturn(new ArrayCollection([$userReminder]));

        $this->mockEventDispatcher->expects($this->once())->method('dispatch')
            ->with(UserReminderEvents::REMINDER_SENT, $this->callback(function (UserReminderEvent $e) use ($userReminder) {
                $this->assertEquals($userReminder, $e->getReminder());
                return true;
            }));

        $service = $this->createTestObject();

        $service->registerReminder('sometype', $mockReminder);
        $this->assertEquals($mockReminder, $service->getReminder(new IdentValue('sometype')));

        $service->send();
    }

    /**
     * @test
     * @group                    UserReminderBundle
     * @group                    UserReminder
     * @depends                  itShouldBeInstantiable
     * @expectedException \Dothiv\CharityWebsiteBundle\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unknown notification: "sometype"
     */
    public function itShouldThrowExceptionOnInvalidType()
    {
        $service = $this->createTestObject();
        $service->getReminder(new IdentValue('sometype'));
    }

    /**
     * @test
     * @group                    UserReminderBundle
     * @group                    UserReminder
     * @depends                  itShouldBeInstantiable
     * @expectedException \Dothiv\CharityWebsiteBundle\Exception\InvalidArgumentException
     * @expectedExceptionMessage There is already a reminder registered with name: "sometype"
     */
    public function itShouldNotAddRemindersTwice()
    {
        $mockReminder = $this->getMock('\Dothiv\UserReminderBundle\Service\UserReminderInterface');

        $service = $this->createTestObject();
        $service->addReminder(new IdentValue('sometype'), $mockReminder);
        $service->addReminder(new IdentValue('sometype'), $mockReminder);
    }

    /**
     * @return UserReminderRegistry
     */
    protected function createTestObject()
    {
        return new UserReminderRegistry($this->mockEventDispatcher);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockEventDispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }
}
