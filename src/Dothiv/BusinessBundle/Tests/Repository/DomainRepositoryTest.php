<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Repository\DomainRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\ValueObject\EmailValue;

class DomainRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group Domain
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Repository\DomainRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Domain
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $domain = new Domain();
        $domain->setOwnerEmail('john.doe@example.com');
        $domain->setOwnerName('John Doe');
        $domain->setName('example.hiv');
        $domain->setRegistrar($this->createRegistrar());
        $repo = $this->getTestObject();
        $repo->persist($domain);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Domain
     * @group   Integration
     * @depends itShouldPersist
     */
    public function itShouldFindUninstalled()
    {
        $domain = new Domain();
        $domain->setOwnerEmail('john.doe@example.com');
        $domain->setOwnerName('John Doe');
        $domain->setName('example.hiv');
        $domain->setRegistrar($this->createRegistrar());
        $repo = $this->getTestObject();
        $repo->persist($domain);
        $repo->flush();

        // Domain has no click-counter configured -> it is not uninstalled
        $this->assertEquals(0, count($repo->findUninstalled()));

        $banner = new Banner();
        $banner->setDomain($domain);
        $domain->setActiveBanner($banner);
        $em = $this->getTestEntityManager();
        $em->persist($banner);
        $em->flush();
        $repo->persist($domain)->flush();

        // Domain has no click-counter configured -> it is not uninstalled
        $this->assertEquals(1, count($repo->findUninstalled()));
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Domain
     * @group   Integration
     * @depends itShouldPersist
     */
    public function itShouldFindByEmail()
    {
        $domain = new Domain();
        $domain->setOwnerEmail('john.doe@example.com');
        $domain->setOwnerName('John Doe');
        $domain->setName('example.hiv');
        $domain->setRegistrar($this->createRegistrar());
        $repo = $this->getTestObject();
        $repo->persist($domain);
        $repo->flush();

        $this->assertEquals(0, count($repo->findByOwnerEmail(new EmailValue('jane.doe@example.com'))));
        $this->assertEquals(1, count($repo->findByOwnerEmail(new EmailValue('john.doe@example.com'))));
    }

    /**
     * @return DomainRepository
     */
    protected function getTestObject()
    {
        /** @var DomainRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivBusinessBundle:Domain');
        $repo->setValidator($this->testValidator);
        return $repo;
    }

    /**
     * @return Registrar
     */
    protected function createRegistrar()
    {
        $registrar = new Registrar();
        $registrar->setExtId('1234-AB');
        $this->getTestEntityManager()->persist($registrar);
        return $registrar;
    }
}
