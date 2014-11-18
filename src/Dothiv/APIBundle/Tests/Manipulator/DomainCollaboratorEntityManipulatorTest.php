<?php

namespace Dothiv\APIBundle\Manipulator\Tests;

use Dothiv\APIBundle\Manipulator\DomainCollaboratorEntityManipulator;
use Dothiv\APIBundle\Request\DefaultUpdateRequest;
use Dothiv\APIBundle\Request\DomainCollaboratorCreateRequest;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use PhpOption\Option;

class DomainCollaboratorEntityManipulatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var UserServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserService;

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @test
     * @group Entity
     * @group AdminBundle
     * @group Manipulator
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\APIBundle\Manipulator\DomainCollaboratorEntityManipulator', $this->createTestObject());
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
        $friend = new User();
        $friend->setEmail('jane.doe@example.com');
        $friend->setFirstname('Jane');
        $friend->setSurname('Doe');

        $owner = new User();
        $owner->setEmail('john.doe@example.com');
        $owner->setFirstname('John');
        $owner->setSurname('Doe');

        $domain = new Domain();
        $domain->setName('example.hiv');
        $domain->setOwner($owner);

        $entity = new DomainCollaborator();
        $data   = new DomainCollaboratorCreateRequest();
        $data->setDomain('example.hiv');
        $data->setEmail('jane.doe@example.com');
        $data->setFirstname('Jane');
        $data->setLastname('Doe');

        $this->mockDomainRepo->expects($this->once())->method('getDomainByName')
            ->with('example.hiv')
            ->willReturn(Option::fromValue($domain));
        $this->mockUserService->expects($this->once())->method('getOrCreateUser')
            ->with('jane.doe@example.com', 'Jane', 'Doe')
            ->willReturn($friend);

        $this->createTestObject()->manipulate($entity, $data);
        $this->assertEquals($friend, $entity->getUser());
        $this->assertEquals($domain, $entity->getDomain());
    }

    /**
     * @test
     * @group                    Entity
     * @group                    AdminBundle
     * @group                    Manipulator
     * @depends                  itShouldManipulateAnEntity
     * @expectedException \Dothiv\APIBundle\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected $data to be a DomainCollaboratorCreateRequest, got "Dothiv\APIBundle\Request\DefaultUpdateRequest"!
     */
    public function itShouldThrowAnExceptionOnInvalidData()
    {
        $entity = new DomainCollaborator();
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
     * @expectedExceptionMessage Expected $entity to be a DomainCollaborator, got "Dothiv\BusinessBundle\Entity\Domain"!
     */
    public function itShouldThrowAnExceptionOnInvalidEntity()
    {
        $entity = new Domain();
        $data   = new DomainCollaboratorCreateRequest();
        $this->createTestObject()->manipulate($entity, $data);
    }

    /**
     * @return DomainCollaboratorEntityManipulator
     */
    protected function createTestObject()
    {
        return new DomainCollaboratorEntityManipulator($this->mockUserService, $this->mockDomainRepo);
    }

    public function setUp()
    {
        $this->mockUserService = $this->getMock('\Dothiv\BusinessBundle\Service\UserServiceInterface');
        $this->mockDomainRepo  = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
    }
}
