<?php

namespace Dothiv\ShopBundle\Tests\Manipulator;


use Dothiv\APIBundle\Request\DefaultUpdateRequest;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Manipulator\OrderManipulator;
use Dothiv\ShopBundle\Request\OrderCreateRequest;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\URLValue;

class OrderManipulatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group ShopBundle
     * @group Manipulator
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\ShopBundle\Manipulator\OrderManipulator', $this->createTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   ShopBundle
     * @group   Manipulator
     * @depends itShouldBeInstantiable
     */
    public function itShouldManipulateAnEntity()
    {
        $order = new Order();

        $model = new OrderCreateRequest();
        $model->setDomain("xn--brger-kva.hiv");
        $model->setClickCounter(true);
        $model->setRedirect("http://jana.com/");
        $model->setDuration(3);
        $model->setFirstname("Jana");
        $model->setLastname("Bürger");
        $model->setEmail('jana.müller@bürger.de');
        $model->setPhone("+49301234567");
        $model->setFax("+4930123456777");
        $model->setLocality("Waldweg 1");
        $model->setLocality2("Hinterhaus");
        $model->setCity("12345 Neustadt");
        $model->setCountry("Germany (Deutschland)");
        $model->setOrganization("Bürger GmbH");
        $model->setVatNo("DE123456789");
        $model->setCurrency(Order::CURRENCY_EUR);
        $model->setStripeToken("tok_14kvt242KFPpMZB00CUopZjt");
        $model->setStripeCard("crd_14kvt242KFPpMZB00CUopZjt");

        $this->createTestObject()->manipulate($order, $model);

        $this->assertEquals($order->getDomain(), new HivDomainValue("xn--brger-kva.hiv"));
        $this->assertEquals($order->getClickCounter(), true);
        $this->assertEquals($order->getRedirect(), new URLValue("http://jana.com/"));
        $this->assertEquals($order->getDuration(), 3);
        $this->assertEquals($order->getFirstname(), "Jana");
        $this->assertEquals($order->getLastname(), "Bürger");
        $this->assertEquals($order->getEmail(), new EmailValue('jana.müller@bürger.de'));
        $this->assertEquals($order->getPhone(), "+49301234567");
        $this->assertEquals($order->getFax()->get(), "+4930123456777");
        $this->assertEquals($order->getLocality(), "Waldweg 1");
        $this->assertEquals($order->getLocality2()->get(), "Hinterhaus");
        $this->assertEquals($order->getCity(), "12345 Neustadt");
        $this->assertEquals($order->getCountry(), "Germany (Deutschland)");
        $this->assertEquals($order->getOrganization()->get(), "Bürger GmbH");
        $this->assertEquals($order->getVatNo()->get(), "DE123456789");
        $this->assertEquals($order->getStripeToken(), new IdentValue("tok_14kvt242KFPpMZB00CUopZjt"));
        $this->assertEquals($order->getStripeCard(), new IdentValue("crd_14kvt242KFPpMZB00CUopZjt"));
    }

    /**
     * @test
     * @group                    Entity
     * @group                    ShopBundle
     * @group                    Manipulator
     * @depends                  itShouldManipulateAnEntity
     * @expectedException \Dothiv\ShopBundle\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected $data to be a OrderCreateRequest, got "Dothiv\APIBundle\Request\DefaultUpdateRequest"!
     */
    public
    function itShouldThrowAnExceptionOnInvalidData()
    {
        $entity = new Order();
        $data   = new DefaultUpdateRequest();
        $this->createTestObject()->manipulate($entity, $data);
    }

    /**
     * @test
     * @group                    Entity
     * @group                    ShopBundle
     * @group                    Manipulator
     * @depends                  itShouldManipulateAnEntity
     * @expectedException \Dothiv\ShopBundle\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected $entity to be a Order, got "Dothiv\BusinessBundle\Entity\Domain"!
     */
    public
    function itShouldThrowAnExceptionOnInvalidEntity()
    {
        $entity = new Domain();
        $data   = new OrderCreateRequest();
        $this->createTestObject()->manipulate($entity, $data);
    }

    /**
     * @return OrderManipulator
     */
    protected
    function createTestObject()
    {
        return new OrderManipulator();
    }
}
