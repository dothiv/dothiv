<?php

namespace Dothiv\PayitforwardBundle\Entity\Tests;

use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\ValueObject\HivDomainValue;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group PayitforwardBundle
     * @group Order
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\PayitforwardBundle\Entity\Order', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   PayitforwardBundle
     * @group   Order
     * @depends itShouldBeInstantiateable
     */
    public function itShouldCountVouchers()
    {
        $order = $this->getTestObject();
        $this->assertEquals(0, $order->getNumVouchers());
        $order->setDomain1(new HivDomainValue('abc.hiv'));
        $this->assertEquals(1, $order->getNumVouchers());
        $order->setDomain2(new HivDomainValue('cde.hiv'));
        $this->assertEquals(2, $order->getNumVouchers());
        $order->setDomain3(new HivDomainValue('efg.hiv'));
        $this->assertEquals(3, $order->getNumVouchers());
    }

    /**
     * @return Order
     */
    protected function getTestObject()
    {
        return new Order();
    }
} 
