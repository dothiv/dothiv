<?php

namespace Dothiv\CharityWebsiteBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Repository\DomainRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\CharityWebsiteBundle\Entity\DomainNotification;
use Dothiv\CharityWebsiteBundle\Repository\DomainNotificationRepository;
use Dothiv\ValueObject\IdentValue;

class DomainNotificationRepositoryTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Dothiv\CharityWebsiteBundle\Repository\DomainNotificationRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   CharityWebsiteBundle
     * @group   DomainNotification
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $notification = new DomainNotification();
        $notification->setDomain($this->createDomain());
        $notification->setType(new IdentValue('configuration'));
        $repo = $this->createTestObject();
        $repo->persist($notification);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @test
     * @group   Entity
     * @group   CharityWebsiteBundle
     * @group   DomainNotification
     * @group   Integration
     * @depends itShouldPersist
     */
    public function itShouldFindByDomain()
    {
        $domain1       = $this->createDomain('acme.hiv', '1234-AB');
        $notification1 = new DomainNotification();
        $notification1->setType(new IdentValue('configuration'));
        $notification1->setDomain($domain1);
        $repo = $this->createTestObject();
        $repo->persist($notification1);
        $domain2       = $this->createDomain('example.hiv', '5678-AB');
        $notification2 = new DomainNotification();
        $notification2->setType(new IdentValue('configuration'));
        $notification2->setDomain($domain2);
        $repo = $this->createTestObject();
        $repo->persist($notification2);
        $repo->flush();
        $configNotifications = $repo->findByDomain($domain1, new IdentValue('configuration'));
        $this->assertEquals(1, count($configNotifications));
        $this->assertEquals($notification1, $configNotifications->first());
        $otherNotifications = $repo->findByDomain($domain1, new IdentValue('other'));
        $this->assertEquals(0, count($otherNotifications));
    }

    /**
     * @return DomainNotificationRepository
     */
    protected function createTestObject()
    {
        /** @var DomainRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivCharityWebsiteBundle:DomainNotification');
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
