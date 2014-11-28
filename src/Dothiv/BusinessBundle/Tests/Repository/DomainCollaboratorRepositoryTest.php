<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use Dothiv\BusinessBundle\Repository\DomainCollaboratorRepository;

class DomainCollaboratorRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group Domain
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Repository\DomainCollaboratorRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   DomainCollaborator
     * @group   Integration
     * @depends itShouldBeInstantiable
     */
    public function itShouldPersist()
    {
        $domainOwner = new User();
        $domainOwner->setHandle('userhandle');
        $domainOwner->setEmail('john.doe@example.com');
        $domainOwner->setFirstname('John');
        $domainOwner->setSurname('Doe');
        $this->getTestEntityManager()->persist($domainOwner);

        $friend = new User();
        $friend->setHandle('friendhandle');
        $friend->setEmail('jane.doe@example.com');
        $friend->setFirstname('Jane');
        $friend->setSurname('Doe');
        $this->getTestEntityManager()->persist($friend);

        $registrar = new Registrar();
        $registrar->setExtId('1234-AB');
        $this->getTestEntityManager()->persist($registrar);

        $domain = new Domain();
        $domain->setOwnerEmail('john.doe@example.com');
        $domain->setOwnerName('John Doe');
        $domain->setName('example.hiv');
        $domain->setRegistrar($registrar);
        $domain->setOwner($domainOwner);
        $this->getTestEntityManager()->persist($domain);

        $collaborator = new DomainCollaborator();
        $collaborator->setUser($friend);
        $collaborator->setDomain($domain);
        $repo = $this->createTestObject();
        $repo->persist($collaborator);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @return DomainCollaboratorRepository
     */
    protected function createTestObject()
    {
        /** @var DomainRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivBusinessBundle:DomainCollaborator');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
}
