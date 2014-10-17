<?php

namespace Dothiv\CharityWebsiteBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Repository\DomainRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\CharityWebsiteBundle\Entity\DomainConfigurationNotification;
use Dothiv\CharityWebsiteBundle\Repository\DomainConfigurationNotificationRepository;

class DomainConfigurationNotificationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group CharityWebsiteBundle
     * @group Domain
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\CharityWebsiteBundle\Repository\DomainConfigurationNotificationRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   CharityWebsiteBundle
     * @group   DomainConfigurationNotification
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $notification = new DomainConfigurationNotification();
        $notification->setDomain($this->createDomain());
        $repo = $this->createTestObject();
        $repo->persist($notification);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @test
     * @group   Entity
     * @group   CharityWebsiteBundle
     * @group   DomainConfigurationNotification
     * @group   Integration
     * @depends itShouldPersist
     */
    public function itShouldFindByDomain()
    {
        $domain1       = $this->createDomain('acme.hiv', '1234-AB');
        $notification1 = new DomainConfigurationNotification();
        $notification1->setDomain($domain1);
        $repo = $this->createTestObject();
        $repo->persist($notification1);
        $domain2       = $this->createDomain('example.hiv', '5678-AB');
        $notification2 = new DomainConfigurationNotification();
        $notification2->setDomain($domain2);
        $repo = $this->createTestObject();
        $repo->persist($notification2);
        $repo->flush();
        $n = $repo->findByDomain($domain1);
        $this->assertEquals(1, count($n));
        $this->assertEquals($notification1, $n->first());
    }

    /**
     * @return DomainConfigurationNotificationRepository
     */
    protected function createTestObject()
    {
        /** @var DomainRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivCharityWebsiteBundle:DomainConfigurationNotification');
        $repo->setValidator($this->testValidator);
        return $repo;
    }

    /**
     * @param string $name
     * @param string $registrarExtId
     *
     * @return Domain
     */
    protected function createDomain($name = 'example.hiv', $registrarExtId = '1234-AB')
    {
        $registrar = new Registrar();
        $registrar->setExtId($registrarExtId);
        $this->getTestEntityManager()->persist($registrar);
        $domain = new Domain();
        $domain->setOwnerEmail('john.doe@example.com');
        $domain->setOwnerName('John Doe');
        $domain->setName($name);
        $domain->setRegistrar($registrar);
        $this->getTestEntityManager()->persist($domain);
        return $domain;
    }
} 
