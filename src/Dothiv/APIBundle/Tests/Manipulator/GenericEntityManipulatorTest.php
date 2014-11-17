<?php

namespace Dothiv\APIBundle\Manipulator\Tests;

use Dothiv\APIBundle\Request\DefaultUpdateRequest;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\APIBundle\Manipulator\GenericEntityManipulator;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\ValueObject\IdentValue;

class GenericEntityManipulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group AdminBundle
     * @group Manipulator
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\APIBundle\Manipulator\GenericEntityManipulator', $this->createTestObject());
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
        $domain = new Domain();
        $domain->setName('other.hiv');
        $data       = new DefaultUpdateRequest();
        $data->name = 'example.hiv';
        $changes    = $this->createTestObject()->manipulate($domain, $data);
        $this->assertEquals('example.hiv', $domain->getName());
        $this->assertEquals(1, count($changes));
        $this->assertInstanceOf('Dothiv\BusinessBundle\Model\EntityPropertyChange', $changes[0]);
        /** @var EntityPropertyChange $change */
        $change = $changes[0];
        $this->assertEquals('other.hiv', $change->getOldValue());
        $this->assertEquals('example.hiv', $change->getNewValue());
        $this->assertEquals(new IdentValue('name'), $change->getProperty());
    }

    /**
     * @test
     * @group                    Entity
     * @group                    AdminBundle
     * @group                    Manipulator
     * @depends                  itShouldManipulateAnEntity
     */
    public function itShouldDiscardInvalidProperty()
    {
        $domain = new Domain();
        $domain->setName('other.hiv');
        $data          = new DefaultUpdateRequest();
        $data->invalid = 'value';
        $this->createTestObject()->manipulate($domain, $data);
        $this->assertObjectNotHasAttribute('invalid', $domain);
    }

    /**
     * @return GenericEntityManipulator
     */
    protected function createTestObject()
    {
        return new GenericEntityManipulator();
    }
}
