<?php

namespace Dothiv\PremiumConfiguratorBundle\Tests\Service;

use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Service\InvoiceService;
use PhpOption\Option;

class InvoiceServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|InvoiceRepositoryInterface
     */
    private $mockInvoiceRepo;

    /**
     * @test
     * @group Invoice
     * @group PremiumConfiguratorBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\PremiumConfiguratorBundle\Service\InvoiceService', $this->getTestObject());
    }

    /**
     * @test
     * @group        Invoice
     * @group        PremiumConfiguratorBundle
     * @depends      itShouldBeInstantiable
     * @dataProvider subscriptionData
     */
    public function itShouldCreateAnInvoiceForSubscription($type, $vatNo, $taxNo, $vatPercent, $vatPrice, $totalPrice)
    {
        // Set up mocks
        $this->mockInvoiceRepo->expects($this->once())->method('persist')
            ->with($this->isInstanceOf('\Dothiv\BusinessBundle\Entity\Invoice'))
            ->willReturnSelf();
        $this->mockInvoiceRepo->expects($this->once())->method('flush');

        // Set up subscription 
        $subscription = new Subscription();
        $address1     = 'Some street';
        $address2     = 'Some locality';
        $country      = 'Some Country';
        $fullname     = 'John Doe';
        $subscription->setFullname($fullname);
        $subscription->setAddress1($address1);
        $subscription->setAddress2($address2);
        $subscription->setCountry($country);
        $subscription->setTaxNo($taxNo);
        $subscription->setVatNo($vatNo);
        $subscription->setType($type);
        $intervalStart = new \DateTime('2014-10-01T12:34:56+02:00');

        // Run        
        $service = $this->getTestObject();
        $invoice = $service->createInvoiceForSubscription($subscription, $intervalStart);
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Entity\Invoice', $invoice);
        $this->assertEquals($fullname, $invoice->getFullname());
        $this->assertEquals($address1, $invoice->getAddress1());
        $this->assertEquals($address2, $invoice->getAddress2());
        $this->assertEquals($country, $invoice->getCountry());
        $expectedVatNo = Option::fromValue($taxNo)->orElse(Option::fromValue($vatNo))->getOrElse(null);
        $this->assertEquals($expectedVatNo, $invoice->getVatNo());
        $this->assertEquals($vatPercent, $invoice->getVatPercent());
        $this->assertEquals($vatPrice, $invoice->getVatPrice());
        $this->assertEquals($totalPrice, $invoice->getTotalPrice());
        $this->assertEquals(1000, $invoice->getItemPrice());
        $expectedDescription = 'Click-Counter premium subscription (monthly Price Plan) from Oct. 1st 2014 to Oct. 31st 2014.';
        $this->assertEquals($expectedDescription, $invoice->getItemDescription());
    }

    /**
     * Provides testdata for itShouldCreateAnInvoiceForSubscription
     *
     * @return array
     */
    public function subscriptionData()
    {
        return array(
            array('noneu', null, null, 0, 0, 1000),
            array('euorgnet', '1234', null, 0, 0, 1000),
            array('euorg', null, null, 19, 190, 1190),
            array('deorg', null, '1234', 19, 190, 1190),
            array('euprivate', null, null, 19, 190, 1190),
        );
    }

    protected function getTestObject()
    {
        return new InvoiceService($this->mockInvoiceRepo, 1000, 19);
    }

    public function setUp()
    {
        $this->mockInvoiceRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface');
    }
}
