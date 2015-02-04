<?php

namespace Dothiv\LandingpageBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Tests\Traits;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepository;

class LandingpageConfigurationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use Traits\RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group LandingpageBundle
     * @group LandingpageConfiguration
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   LandingpageBundle
     * @group   LandingpageConfiguration
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {

        $domainOwner = new User();
        $domainOwner->setHandle('userhandle');
        $domainOwner->setEmail('john.doe@example.com');
        $domainOwner->setFirstname('John');
        $domainOwner->setSurname('Doe');
        $this->getTestEntityManager()->persist($domainOwner);

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

        $lc = new LandingpageConfiguration();
        $lc->setDomain($domain);
        $lc->setClickCounter(true);
        $lc->setName('Caro');
        $lc->setText('Example text');
        $repo = $this->createTestObject();
        $repo->persist($lc);
        $repo->flush();

        /** @var LandingpageConfiguration $slc */
        $slc = $repo->findByDomain($domain)->get();
        $this->assertEquals($slc->getDomain(), $domain);
        $this->assertEquals($slc->getClickCounter(), true);
        $this->assertEquals('Caro', $slc->getName());
        $this->assertEquals('Example text', $slc->getText()->get());
    }

    /**
     * @return LandingpageConfigurationRepository
     */
    protected function createTestObject()
    {
        /** @var LandingpageConfigurationRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivLandingpageBundle:LandingpageConfiguration');
        $repo->setValidator($this->testValidator);
        return $repo;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->testValidator = $this->getTestContainer()->get('validator');
    }
}
