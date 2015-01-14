<?php

namespace Dothiv\CharityWebsiteBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Repository\DomainRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\ValueObject\IdentValue;

class UserReminderRepositoryTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Dothiv\UserReminderBundle\Repository\UserReminderRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   CharityWebsiteBundle
     * @group   SendClickCounterConfigurationCommand
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $reminder = new UserReminder();
        $reminder->setIdent($this->createDomain());
        $reminder->setType(new IdentValue('configuration'));
        $repo = $this->createTestObject();
        $repo->persist($reminder);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @test
     * @group   Entity
     * @group   CharityWebsiteBundle
     * @group   SendClickCounterConfigurationCommand
     * @group   Integration
     * @depends itShouldPersist
     */
    public function itShouldFindByDomain()
    {
        $domain1   = $this->createDomain('acme.hiv', '1234-AB');
        $reminder1 = new UserReminder;
        $reminder1->setType(new IdentValue('configuration'));
        $reminder1->setIdent($domain1);
        $repo = $this->createTestObject();
        $repo->persist($reminder1);
        $domain2   = $this->createDomain('example.hiv', '5678-AB');
        $reminder2 = new UserReminder();
        $reminder2->setType(new IdentValue('configuration'));
        $reminder2->setIdent($domain2);
        $repo = $this->createTestObject();
        $repo->persist($reminder2);
        $repo->flush();
        $configNotifications = $repo->findByTypeAndItem(new IdentValue('configuration'), $domain1);
        $this->assertEquals(1, count($configNotifications));
        $this->assertEquals($reminder1, $configNotifications->first());
        $otherNotifications = $repo->findByTypeAndItem(new IdentValue('other'), $domain1);
        $this->assertEquals(0, count($otherNotifications));
    }

    /**
     * @return UserReminderRepositoryInterface
     */
    protected function createTestObject()
    {
        /** @var DomainRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivUserReminderBundle:UserReminder');
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
