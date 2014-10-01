<?php

namespace Dothiv\BusinessBundle\Entity\Tests;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Tests\ObjectManipulator;

class InvoiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group Entity
     * @group BusinessBundle
     * @group Invoice
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Entity\Invoice', $this->getTestObject());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Invoice
     * @depends itShouldBeInstantiateable
     */
    public function itShouldGenerateAnInvoiceNo()
    {
        $invoice = $this->getTestObject();
        ObjectManipulator::setProtectedProperty($invoice, 'id', 17);
        $invoice->setCreated(new  \DateTime('2003-11-17T01:23:45+02:00'));
        $this->assertEquals('W20031117-17', $invoice->getNo());
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Invoice
     * @depends itShouldBeInstantiateable
     */
    public function itShouldValidateTotal()
    {
        $eci = $this->getMockBuilder('\Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $eci->expects($this->once())
            ->method('addViolationAt')
            ->with('totalPrice', 'Expected value to be %expected%, but it is %actual%!', array('%expected%' => 1200, '%actual%' => 1300));
        $invoice = $this->getTestObject();
        // Empty invoice is valid
        $invoice->isTotalValid($eci);
        // Valid total
        $invoice->setItemPrice(1000);
        $invoice->setVatPrice(200);
        $invoice->setTotalPrice(1200);
        $invoice->isTotalValid($eci);
        // Invalid total
        $invoice->setTotalPrice(1300);
        $invoice->isTotalValid($eci);
    }

    /**
     * @test
     * @group   Entity
     * @group   BusinessBundle
     * @group   Invoice
     * @depends itShouldBeInstantiateable
     */
    public function itShouldValidateVat()
    {
        $eci = $this->getMockBuilder('\Symfony\Component\Validator\ExecutionContextInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $eci->expects($this->once())
            ->method('addViolationAt')
            ->with('vatPrice', 'Expected value to be %expected%, but it is %actual%!', array('%expected%' => 190, '%actual%' => 200));
        $invoice = $this->getTestObject();
        // Empty invoice is valid
        $invoice->isVatValid($eci);
        // Valid total
        $invoice->setItemPrice(1000);
        $invoice->setVatPrice(200);
        $invoice->setVatPercent(20);
        $invoice->setTotalPrice(1200);
        $invoice->isVatValid($eci);
        // Invalid total
        $invoice->setVatPercent(19);
        $invoice->isVatValid($eci);
    }

    /**
     * @return Invoice
     */
    protected function getTestObject()
    {
        return new Invoice();
    }
} 
