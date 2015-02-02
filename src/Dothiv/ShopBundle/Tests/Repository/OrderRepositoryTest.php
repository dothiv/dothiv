<?php

namespace Dothiv\ShopBundle\Repository\Tests;

use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Repository\OrderRepository;
use Dothiv\BusinessBundle\Tests\Traits;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\URLValue;

class OrderRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use Traits\RepositoryTestTrait;

    /**
     * @test
     * @group Entity
     * @group ShopBundle
     * @group Order
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\ShopBundle\Repository\OrderRepository', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   ShopBundle
     * @group   Order
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersist()
    {
        $order = new Order();
        $order->setDomain(new HivDomainValue("xn--brger-kva.hiv"));
        $order->setClickCounter(true);
        $order->setRedirect(new URLValue("http://jana.com/"));
        $order->setDuration(3);
        $order->setFirstname("Jana");
        $order->setLastname("Bürger");
        $order->setEmail(new EmailValue('jana.müller@bürger.de'));
        $order->setPhone("+49301234567");
        $order->setFax("+4930123456777");
        $order->setLocality("Waldweg 1");
        $order->setLocality2("Hinterhaus");
        $order->setCity("12345 Neustadt");
        $order->setCountry(new IdentValue('DE'));
        $order->setOrganization("Bürger GmbH");
        $order->setVatNo("DE123456789");
        $order->setCurrency(new IdentValue(Order::CURRENCY_EUR));
        $order->setStripeToken(new IdentValue("tok_14kvt242KFPpMZB00CUopZjt"));
        $order->setStripeCard(new IdentValue("crd_14kvt242KFPpMZB00CUopZjt"));
        $order->setStripeCharge(new IdentValue("crg_14kvt242KFPpMZB00CUopZjt"));
        $repo = $this->getTestObject();
        $repo->persist($order);
        $repo->flush();

        /** @var Order[] $all */
        $all = $repo->findAll();
        $this->assertEquals(1, count($all));
        /** @var Order $storedOrder */
        $storedOrder = $all[0];
        $this->assertEquals($storedOrder->getDomain(), new HivDomainValue("xn--brger-kva.hiv"));
        $this->assertEquals($storedOrder->getClickCounter(), true);
        $this->assertEquals($storedOrder->getRedirect()->get(), new URLValue("http://jana.com/"));
        $this->assertFalse($storedOrder->getGift());
        $this->assertEquals($storedOrder->getDuration(), 3);
        $this->assertEquals($storedOrder->getFirstname(), "Jana");
        $this->assertEquals($storedOrder->getLastname(), "Bürger");
        $this->assertEquals($storedOrder->getEmail(), new EmailValue('jana.müller@bürger.de'));
        $this->assertEquals($storedOrder->getPhone(), "+49301234567");
        $this->assertEquals($storedOrder->getFax()->get(), "+4930123456777");
        $this->assertEquals($storedOrder->getLocality(), "Waldweg 1");
        $this->assertEquals($storedOrder->getLocality2()->get(), "Hinterhaus");
        $this->assertEquals($storedOrder->getCity(), "12345 Neustadt");
        $this->assertEquals($storedOrder->getCountry()->toScalar(), "DE");
        $this->assertEquals($storedOrder->getOrganization()->get(), "Bürger GmbH");
        $this->assertEquals($storedOrder->getVatNo()->get(), "DE123456789");
        $this->assertEquals($storedOrder->getStripeToken(), new IdentValue("tok_14kvt242KFPpMZB00CUopZjt"));
        $this->assertEquals($storedOrder->getStripeCard(), new IdentValue("crd_14kvt242KFPpMZB00CUopZjt"));
        $this->assertEquals($storedOrder->getStripeCharge()->get(), new IdentValue("crg_14kvt242KFPpMZB00CUopZjt"));
    }

    /**
     * @test
     * @group   Entity
     * @group   ShopBundle
     * @group   Order
     * @group   Integration
     * @depends itShouldBeInstantiateable
     */
    public function itShouldPersistA4lifeDomain()
    {
        $order = new Order();
        $order->setDomain(HivDomainValue::createFromUTF8("bürger4life.hiv"));
        $order->setDuration(3);
        $order->setGift(true);
        $order->setPresenteeFirstname("Martin");
        $order->setPresenteeLastname("Müller");
        $order->setPresenteeEmail(new EmailValue('martin.müller@bürger.de'));
        $order->setLandingpageOwner('Donald Duck');
        $order->setFirstname("Jana");
        $order->setLastname("Bürger");
        $order->setEmail(new EmailValue('jana.müller@bürger.de'));
        $order->setPhone("+49301234567");
        $order->setFax("+4930123456777");
        $order->setLocality("Waldweg 1");
        $order->setLocality2("Hinterhaus");
        $order->setCity("12345 Neustadt");
        $order->setCountry(new IdentValue('DE'));
        $order->setOrganization("Bürger GmbH");
        $order->setVatNo("DE123456789");
        $order->setCurrency(new IdentValue(Order::CURRENCY_EUR));
        $order->setStripeToken(new IdentValue("tok_14kvt242KFPpMZB00CUopZjt"));
        $order->setStripeCard(new IdentValue("crd_14kvt242KFPpMZB00CUopZjt"));
        $order->setStripeCharge(new IdentValue("crg_14kvt242KFPpMZB00CUopZjt"));
        $repo = $this->getTestObject();
        $repo->persist($order);
        $repo->flush();

        /** @var Order[] $all */
        $all = $repo->findAll();
        $this->assertEquals(1, count($all));
        /** @var Order $storedOrder */
        $storedOrder = $all[0];
        $this->assertTrue($storedOrder->getGift());
        $this->assertEquals($storedOrder->getPresenteeFirstname()->get(), "Martin");
        $this->assertEquals($storedOrder->getPresenteeLastname()->get(), "Müller");
        $this->assertEquals($storedOrder->getPresenteeEmail()->get(), new EmailValue('martin.müller@bürger.de'));
        $this->assertEquals('Donald Duck', $storedOrder->getLandingpageOwner()->get());

    }

    /**
     * @return OrderRepository
     */
    protected function getTestObject()
    {
        /** @var OrderRepository $repo */
        $repo = $this->getTestEntityManager()->getRepository('DothivShopBundle:Order');
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
