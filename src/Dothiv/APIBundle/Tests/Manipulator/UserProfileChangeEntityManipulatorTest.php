<?php

namespace Dothiv\APIBundle\Manipulator\Tests;

use Dothiv\APIBundle\Manipulator\UserProfileChangeEntityManipulator;
use Dothiv\APIBundle\Request\DefaultUpdateRequest;
use Dothiv\APIBundle\Request\UserProfileChangeConfirmRequest;
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
        $data            = new UserProfileChangeConfirmRequest();
        $data->confirmed = 'some-token';
        $changes         = $this->createTestObject()->manipulate($entity, $data);
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
     * @expectedExceptionMessage Expected $data to be a UserProfileChangeConfirmRequest, got "Dothiv\APIBundle\Request\DefaultUpdateRequest"!
     */
    public function itShouldThrowAnExceptionOnInvalidData()
    {
        $entity = new UserProfileChange();
        $data   = new DefaultUpdateRequest();
        $this->createTestObject()->manipulate($entity, $data);
    }

    /**
     * @test
     * @group                    Entity
     * @group                    AdminBundle
     * @group                    Manipulator
     * @depends                  itShouldManipulateAnEntity
     * @expectedException \Dothiv\APIBundle\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected $entity to be a UserProfileChange, got "Dothiv\BusinessBundle\Entity\Domain"!
     */
    public function itShouldThrowAnExceptionOnInvalidEntity()
    {
        $entity = new Domain();
        $data   = new UserProfileChangeConfirmRequest();
        $this->createTestObject()->manipulate($entity, $data);
    }

    /**
     * @return UserProfileChangeEntityManipulator
     */
    protected function createTestObject()
    {
        return new UserProfileChangeEntityManipulator();
    }
}
