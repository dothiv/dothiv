<?php

namespace Dothiv\APIBundle\Manipulator\Tests;

use Dothiv\APIBundle\Manipulator\UserProfileChangeEntityManipulator;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\ValueObject\IdentValue;

class UserProfileChangeEntityManipulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group AdminBundle
     * @group Manipulator
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\APIBundle\Manipulator\UserProfileChangeEntityManipulator', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   AdminBundle
     * @group   Manipulator
     * @depends itShouldBeInstantiable
     */
    public function itShouldManipulateAnEntity()
    {
        $entity = new UserProfileChange();
        $entity->setToken(new IdentValue('some-token'));
        $properties = array(
            'confirmed' => 'some-token'
        );
        $changes    = $this->createTestObject()->manipulate($entity, $properties);
        $this->assertTrue($entity->getConfirmed());
        $this->assertEquals(1, count($changes));
        $this->assertInstanceOf('Dothiv\BusinessBundle\Model\EntityPropertyChange', $changes[0]);
        /** @var EntityPropertyChange $change */
        $change = $changes[0];
        $this->assertFalse($change->getOldValue());
        $this->assertTrue($change->getNewValue());
        $this->assertEquals(new IdentValue('confirmed'), $change->getProperty());
    }

    /**
     * @test
     * @group                    Entity
     * @group                    AdminBundle
     * @group                    Manipulator
     * @depends                  itShouldManipulateAnEntity
     * @expectedException \Dothiv\APIBundle\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unknown property "invalid"!
     */
    public function itShouldThrowAnExceptionOnInvalidProperty()
    {
        $domain     = new Domain();
        $properties = array(
            'invalid' => 'value'
        );
        $this->createTestObject()->manipulate($domain, $properties);
    }

    /**
     * @return UserProfileChangeEntityManipulator
     */
    protected function createTestObject()
    {
        return new UserProfileChangeEntityManipulator();
    }
}
