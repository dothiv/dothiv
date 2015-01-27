<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\DeletedDomain;
use Dothiv\BusinessBundle\Repository\DeletedDomainRepository;
use Dothiv\BusinessBundle\Repository\DomainRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\ValueObject\HivDomainValue;

class DeletedDomainRepositoryTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Repository\DeletedDomainRepository', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   DeletedDomain
     * @group   Integration
     * @depends itShouldBeInstantiable
     */
    public function itShouldPersist()
    {
        $entity  = new DeletedDomain(new HivDomainValue('tld.hiv'));
        $entity2 = new DeletedDomain(new HivDomainValue('tld.hiv'));
        $repo    = $this->createTestObject();
        $repo->persist($entity);
        $repo->persist($entity2);
        $repo->flush();
        $items = $repo->findAll();
        /** @var DeletedDomain[] $items */
        $this->assertEquals(2, count($items));
        $this->assertEquals('tld.hiv', $items[0]->getDomain()->toScalar());
        $this->assertEquals('tld.hiv', $items[1]->getDomain()->toScalar());
    }

    /**
     * @return DeletedDomainRepository
     */
    protected function createTestObject()
    {
        /** @var DomainRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivBusinessBundle:DeletedDomain');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
}
