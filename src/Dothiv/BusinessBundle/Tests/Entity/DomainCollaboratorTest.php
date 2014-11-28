<?php

namespace Dothiv\BusinessBundle\Entity\Tests;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Tests\ObjectManipulator;

class DomainCollaboratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group DomainCollaborator
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Entity\DomainCollaborator', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   DomainCollaborator
     * @depends itShouldBeInstantiateable
     */
    public function itShouldValidateUser()
    {
        $eci = $this->getMockBuilder('\Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $eci->expects($this->once())
            ->method('addViolationAt')
            ->with('user', 'User "%user%" must not be owner of domain "%domain%"!', array('%user%' => 'userhandle', '%domain%' => 'example.hiv'));


        $domainOwner = new User();
        $domainOwner->setHandle('userhandle');
        $domainOwner->setEmail('john.doe@example.com');
        $domainOwner->setFirstname('John');
        $domainOwner->setSurname('Doe');
        ObjectManipulator::setProtectedProperty($domainOwner, 'id', 17);

        $friend = new User();
        $friend->setHandle('friendhandle');
        $friend->setEmail('jane.doe@example.com');
        $friend->setFirstname('Jane');
        $friend->setSurname('Doe');
        ObjectManipulator::setProtectedProperty($friend, 'id', 42);

        $domain = new Domain();
        $domain->setName('example.hiv');
        $domain->setOwner($domainOwner);

        $collaborator = $this->getTestObject();
        $collaborator->setUser($domainOwner);
        $collaborator->setDomain($domain);

        $collaborator->isValid($eci);
    }


    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   DomainCollaborator
     * @depends itShouldBeInstantiateable
     */
    public function itShouldValidateDomain()
    {
        $eci = $this->getMockBuilder('\Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $eci->expects($this->once())
            ->method('addViolationAt')
            ->with('domain', 'Domain "%domain%" must have an owner!', array('%domain%' => 'example.hiv'));

        $friend = new User();
        $friend->setHandle('friendhandle');
        $friend->setEmail('jane.doe@example.com');
        $friend->setFirstname('Jane');
        $friend->setSurname('Doe');
        ObjectManipulator::setProtectedProperty($friend, 'id', 42);

        $domain = new Domain();
        $domain->setName('example.hiv');

        $collaborator = $this->getTestObject();
        $collaborator->setUser($friend);
        $collaborator->setDomain($domain);

        $collaborator->isValid($eci);
    }

    /**
     * @return DomainCollaborator
     */
    protected function getTestObject()
    {
        return new DomainCollaborator();
    }
} 
