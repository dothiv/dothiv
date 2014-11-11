<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\ValueObject\IdentValue;

class UserProfileChangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group Domain
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Entity\UserProfileChange', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Domain
     * @depends itShouldBeInstantiable
     */
    public function itShouldConfirm()
    {
        $change = $this->createTestObject();
        $change->setToken(new IdentValue('some-token'));
        $this->assertFalse($change->getConfirmed());
        $change->confirm(new IdentValue('some-token'));
        $this->assertTrue($change->getConfirmed());
    }

    /**
     * @test
     * @group                    Entity
     * @group                    BusinessBundle
     * @group                    Domain
     * @depends                  itShouldBeInstantiable
     * @expectedException \Dothiv\APIBundle\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid token: "some-other-token"!
     */
    public function itShouldNotConfirmOnInvalidToken()
    {
        $change = $this->createTestObject();
        $change->setToken(new IdentValue('some-token'));
        $this->assertFalse($change->getConfirmed());
        $change->confirm(new IdentValue('some-other-token'));
        $this->fail();
    }

    /**
     * @return UserProfileChange
     */
    protected function createTestObject()
    {
        return new UserProfileChange();
    }
}
