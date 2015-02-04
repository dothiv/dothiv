<?php

namespace Dothiv\PayitforwardBundle\Repository\Tests;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Entity\Voucher;
use Dothiv\PayitforwardBundle\Exception\InsufficientResourcesException;
use Dothiv\PayitforwardBundle\Repository\VoucherRepository;
use Dothiv\BusinessBundle\Tests\Traits\RepositoryTestTrait;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;

class VoucherRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group PayitforwardBundle
     * @group Voucher
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\PayitforwardBundle\Repository\VoucherRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   PayitforwardBundle
     * @group   Voucher
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $order = $this->createOrder();

        $voucher = new Voucher();
        $voucher->setCode(new IdentValue('Promo-HIV-AABBCC'));
        $voucher->setOrder($order);
        $repo = $this->getTestObject();
        $repo->persist($voucher);
        $repo->flush();
        $all = $repo->findAll();
        $this->assertEquals(1, count($all));
        /** @var Voucher $e */
        $e = $all[0];
        $this->assertEquals('Promo-HIV-AABBCC', (string)$e->getCode(), 'Invalid code');
        $this->assertEquals($order, $e->getOrder(), 'Invalid order');
    }

    /**
     * @test
     * @group   Entity
     * @group   PayitforwardBundle
     * @group   Voucher
     * @group   Integration
     * @depends itShouldBeInstantiateable
     * @expectedException \Dothiv\PayitforwardBundle\Exception\InsufficientResourcesException
     */
    public function itShouldReturnUnassigned()
    {
        $repo       = $this->getTestObject();
        $unassigned = $repo->findUnassigned(0);
        $this->assertEquals(0, $unassigned->count());

        $voucher1 = new Voucher();
        $voucher1->setCode(new IdentValue('Promo-HIV-AABBCC'));
        $voucher2 = new Voucher();
        $voucher2->setCode(new IdentValue('Promo-HIV-CCDDEE'));
        $repo->persist($voucher1)->persist($voucher2)->flush();

        $unassigned = $repo->findUnassigned(2);
        $this->assertEquals(2, $unassigned->count());
        $repo->findUnassigned(3); // Throws exception
    }

    /**
     * @test
     * @group   Entity
     * @group   PayitforwardBundle
     * @group   Voucher
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldReturnAssigned()
    {
        $order = $this->createOrder();

        $repo     = $this->getTestObject();
        $assigned = $repo->findAssigned($order);
        $this->assertEquals(0, $assigned->count());

        $voucher1 = new Voucher();
        $voucher1->setCode(new IdentValue('Promo-HIV-AABBCC'));
        $voucher1->setOrder($order);
        $voucher2 = new Voucher();
        $voucher2->setCode(new IdentValue('Promo-HIV-CCDDEE'));
        $voucher2->setOrder($order);
        $repo->persist($voucher1)->persist($voucher2)->flush();

        $assigned = $repo->findAssigned($order);
        $this->assertEquals(2, $assigned->count());
    }

    /**
     * @return VoucherRepository
     */
    protected function getTestObject()
    {
        /** @var VoucherRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivPayitforwardBundle:Voucher');
        $repo->setValidator($this->testValidator);
        return $repo;
    }

    /**
     * @return Order
     */
    protected function createOrder()
    {
        $user = new User();
        $user->setHandle('userhandle');
        $user->setEmail('someone@example.com');
        $user->setFirstname('John');
        $user->setSurname('Doe');
        $this->getTestEntityManager()->persist($user);

        $order = new Order();
        $order->setUser($user);
        $order->setFirstname('John');
        $order->setSurname('Doe');
        $order->setEmail(new EmailValue('john.doe@example.com'));
        $order->setFullname('John Doe');
        $order->setAddress1('123 Some Street');
        $order->setCountry(new IdentValue('DE'));
        $order->setToken('tok_14kcI342KFPpMZB0scN8KPTM');
        $order->setLiveMode('0');
        $this->getTestEntityManager()->persist($order);

        $this->getTestEntityManager()->flush();

        return $order;
    }
}
