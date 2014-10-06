<?php

namespace Dothiv\PayitforwardBundle\Repository\Tests;

use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Repository\OrderRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;

class OrderRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group PayitforwardBundle
     * @group Order
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\PayitforwardBundle\Repository\OrderRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   PayitforwardBundle
     * @group   Order
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $order = new Order();
        $order->setFirstname('John');
        $order->setSurname('Doe');
        $order->setEmail(new EmailValue('john.doe@example.com'));
        $order->setDomain(new HivDomainValue('example.hiv'));
        $order->setDomainDonor('Some Friend');
        $order->setDomainDonorTwitter('@friend');
        $order->setType('deorg');
        $order->setFullname('John Doe');
        $order->setAddress1('123 Some Street');
        $order->setAddress2('123 Some City');
        $order->setCountry('Germany (Deutschland);');
        $order->setVatNo('1243');
        $order->setTaxNo('45678');
        $order->setDomain1(new HivDomainValue('super.hiv'));
        $order->setDomain1Name('Super User');
        $order->setDomain1Company('Super Company');
        $order->setDomain1Twitter('@super');
        $order->setDomain2(new HivDomainValue('awesome.hiv'));
        $order->setDomain2Name('Awesome User');
        $order->setDomain2Company('Awesome Company');
        $order->setDomain2Twitter('@awesome');
        $order->setDomain3(new HivDomainValue('rad.hiv'));
        $order->setDomain3Name('Rad User');
        $order->setDomain3Company('Rad Company');
        $order->setDomain3Twitter('@rad');
        $order->setToken('tok_14kcI342KFPpMZB0scN8KPTM');
        $order->setLiveMode('0');
        $repo = $this->getTestObject();
        $repo->persist($order);
        $repo->flush();
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @return OrderRepository
     */
    protected function getTestObject()
    {
        /** @var OrderRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivPayitforwardBundle:Order');
        $repo->setValidator($this->testValidator);
        return $repo;
    }
} 
