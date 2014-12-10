<?php

namespace Dothiv\HivDomainStatusBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\HivDomainStatusBundle\Repository\HivDomainCheckRepository;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use PhpOption\None;

class HivDomainCheckRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group HivDomainStatusBundle
     * @group Domain
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\HivDomainStatusBundle\Repository\HivDomainCheckRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   HivDomainStatusBundle
     * @group   HivDomainCheck
     * @group   Integration
     * @depends itShouldBeInstantiable
     */
    public function itShouldPersist()
    {
        $domain = $this->createDomain();

        $check = new HivDomainCheck();
        $check->setDomain($domain);
        $check->setUrl('http://example.com/');
        $repo = $this->createTestObject();
        $repo->persist($check);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @test
     * @group   Entity
     * @group   HivDomainStatusBundle
     * @group   HivDomainCheck
     * @group   Integration
     * @depends itShouldBeInstantiable
     */
    public function itShouldFindLatestByDomain()
    {
        $domain = $this->createDomain();

        $repo = $this->createTestObject();
        $this->assertEquals(None::create(), $repo->findLatestForDomain($domain));

        $check1 = new HivDomainCheck();
        $check1->setDomain($domain);
        $check1->setUrl('http://example.com/');
        $repo->persist($check1);

        $check2 = new HivDomainCheck();
        $check2->setDomain($domain);
        $check2->setUrl('http://example.com/');
        $check2->setValid(true);
        $repo->persist($check2);
        $repo->flush();
        $this->assertEquals($check2, $repo->findLatestForDomain($domain)->get());
    }

    /**
     * @return HivDomainCheckRepository
     */
    protected function createTestObject()
    {
        /** @var HivDomainCheckRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivHivDomainStatusBundle:HivDomainCheck');
        $repo->setValidator($this->testValidator);
        return $repo;
    }

    /**
     * @return Domain
     */
    protected function createDomain()
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

        $this->getTestEntityManager()->flush();

        return $domain;
    }
}
