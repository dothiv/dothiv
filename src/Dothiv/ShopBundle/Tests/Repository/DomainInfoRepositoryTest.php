<?php

namespace Dothiv\ShopBundle\Repository\Tests;

use Dothiv\ShopBundle\Entity\DomainInfo;
use Dothiv\ShopBundle\Repository\DomainInfoRepository;
use Dothiv\BusinessBundle\Tests\Traits;
use Dothiv\ValueObject\HivDomainValue;

class DomainInfoRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use Traits\RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group ShopBundle
     * @group DomainInfo
     * @group Shop
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\ShopBundle\Repository\DomainInfoRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   ShopBundle
     * @group   DomainInfo
     * @group   Integration
     * @group   Shop
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $domainInfo = new DomainInfo();
        $domainInfo->setName(new HivDomainValue('caro.hiv'));
        $domainInfo->setRegistered(true);
        $domainInfo->setPremium(true);
        $domainInfo->setBlocked(true);
        $domainInfo->setTrademark(true);
        $repo = $this->getTestObject();
        $repo->persist($domainInfo);
        $repo->flush();

        /** @var DomainInfo[] $all */
        $all = $repo->findAll();
        $this->assertEquals(1, count($all));
        $this->assertEquals('caro.hiv', (string)$all[0]->getName());
        $this->assertTrue($all[0]->getRegistered());
        $this->assertTrue($all[0]->getPremium());
        $this->assertTrue($all[0]->getBlocked());
        $this->assertTrue($all[0]->getTrademark());
        $this->assertFalse($all[0]->getAvailable());
    }

    /**
     * @return DomainInfoRepository
     */
    protected function getTestObject()
    {
        /** @var DomainInfoRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivShopBundle:DomainInfo');
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
