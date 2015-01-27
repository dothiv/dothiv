<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\DomainWhois;
use Dothiv\BusinessBundle\Repository\DomainRepository;
use Dothiv\BusinessBundle\Repository\DomainWhoisRepository;
use Dothiv\BusinessBundle\Service\WhoisReportParser;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\ValueObject\HivDomainValue;

class DomainWhoisRepositoryTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Repository\DomainWhoisRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   DomainWhois
     * @group   Integration
     * @depends itShouldBeInstantiable
     */
    public function itShouldPersist()
    {
        $whois  = new WhoisReportParser();
        $entity = DomainWhois::create(new HivDomainValue('tld.hiv'), $whois->parse(file_get_contents(__DIR__ . '/../Service/data/tld.hiv.whois')));
        $repo   = $this->createTestObject();
        $repo->persist($entity);
        $repo->flush();
        $items = $repo->findAll();
        /** @var DomainWhois $storedWhois */
        $storedWhois = $repo->findByDomain(new HivDomainValue('tld.hiv'))->get();
        $this->assertEquals(1, count($items));
        $report = $storedWhois->getWhois();
        $this->assertEquals('tld.hiv', $storedWhois->getDomain()->toScalar());
        $this->assertEquals('TLD.HIV', $report->get('Domain Name'));
        $this->assertEquals('TLD dotHIV Registry GmbH', $report->get('Registrant Name'));
        $this->assertEquals('domains@tld.hiv', $report->get('Registrant Email'));
        $nameservers = [
            'NS-CLOUD-E1.GOOGLEDOMAINS.COM',
            'NS-CLOUD-E2.GOOGLEDOMAINS.COM',
            'NS-CLOUD-E3.GOOGLEDOMAINS.COM',
            'NS-CLOUD-E4.GOOGLEDOMAINS.COM'
        ];
        $this->assertEquals($nameservers, $report->get('Name Server'));
    }

    /**
     * @return DomainWhoisRepository
     */
    protected function createTestObject()
    {
        /** @var DomainRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivBusinessBundle:DomainWhois');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
}
