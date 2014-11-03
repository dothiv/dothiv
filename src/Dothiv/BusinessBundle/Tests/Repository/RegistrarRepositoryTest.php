<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Registrar;
use Dothiv\BusinessBundle\Repository\RegistrarRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;

class RegistrarRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group Registrar
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Repository\RegistrarRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Registrar
     * @group   Integration
     * @depends itShouldBeInstantiable
     */
    public function itShouldPersist()
    {
        $registrar = new Registrar();
        $registrar->setExtId('1234-AB');
        $registrar->setName('Example Registrar');
        $repo = $this->getTestObject();
        $repo->persist($registrar);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Registrar
     * @group   Integration
     * @depends itShouldBeInstantiable
     */
    public function itShouldCreateNewRegistrarIfNotExist()
    {
        $repo      = $this->getTestObject();
        $registrar = $repo->getByExtId('1234-AB');
        $this->assertEquals(1, count($repo->findAll()));
        $this->assertEquals('1234-AB', $registrar->getExtId());
    }

    /**
     * @return RegistrarRepository
     */
    protected function getTestObject()
    {
        /** @var RegistrarRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivBusinessBundle:Registrar');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
} 
