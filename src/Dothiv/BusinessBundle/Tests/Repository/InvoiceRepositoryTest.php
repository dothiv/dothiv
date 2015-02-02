<?php

namespace Dothiv\BusinessBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\ValueObject\IdentValue;

class InvoiceRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group Invoice
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Repository\InvoiceRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Invoice
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $invoice = new Invoice();
        $invoice->setFullname('John Doe');
        $invoice->setAddress1('Some Address');
        $invoice->setCountry(new IdentValue('DE'));
        $invoice->setItemDescription('Some Item');
        $repo = $this->getTestObject();
        $repo->persist($invoice);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Invoice
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldGetById()
    {
        $invoice = new Invoice();
        $invoice->setFullname('John Doe');
        $invoice->setAddress1('Some Address');
        $invoice->setCountry(new IdentValue('DE'));
        $invoice->setItemDescription('Some Item');
        $repo = $this->getTestObject();
        $repo->persist($invoice);
        $repo->flush();
        $this->assertEquals($invoice->getId(), $repo->getById($invoice->getId())->getId());
    }

    /**
     * @test
     * @group                    Entity
     * @group                    BusinessBundle
     * @group                    Invoice
     * @group                    Integration
     * @depends                  itShouldGetById
     * @expectedException \Dothiv\BusinessBundle\Exception\EntityNotFoundException
     * @expectedExceptionMessage Entity was not found.
     */
    public function getByIdShouldThrowAnExceptionIfEntityNotFound()
    {
        $this->getTestObject()->getById(17);
        $this->fail('getById() should throw an exception of entity is not found');
    }

    /**
     * @return InvoiceRepository
     */
    protected function getTestObject()
    {
        /** @var InvoiceRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivBusinessBundle:Invoice');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
}
