<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\UserNotification;

class UserNotificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group Domain
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Entity\UserNotification', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Domain
     * @depends itShouldBeInstantiable
     */
    public function itShouldValidate()
    {
        $eci = $this->getMock('\Symfony\Component\Validator\ExecutionContextInterface');
        $eci->expects($this->once())
            ->method('addViolationAt')
            ->with('properties', 'UserNotification has no properties!');
        $notification = $this->createTestObject();
        $notification->isValid($eci);
    }

    /**
     * @return UserNotification
     */
    protected function createTestObject()
    {
        return new UserNotification();
    }
}
