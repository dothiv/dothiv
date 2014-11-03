<?php

namespace Dothiv\PayitforwardBundle\Tests\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\PayitforwardBundle\Command\SendInvoiceCommand;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Repository\OrderRepositoryInterface;
use Dothiv\PayitforwardBundle\Service\Mailer\OrderMailerInterface;
use Dothiv\PayitforwardBundle\Service\OrderServiceInterface;
use Dothiv\ValueObject\EmailValue;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SendInvoiceCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var InputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInput;

    /**
     * @var OutputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOutput;

    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContainer;

    /**
     * @var InvoiceRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInvoiceRepo;

    /**
     * @var OrderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOrderRepo;

    /**
     * @var OrderServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOrderService;

    /**
     * @var OrderMailerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOrderMailer;

    /**
     * @test
     * @group DothivPayitforwardBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\PayitforwardBundle\Command\SendInvoiceCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivPayitforwardBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldConfigureBanners()
    {
        $containerMap = array(
            array('dothiv.repository.payitforward.order', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockOrderRepo),
            array('dothiv.repository.invoice', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockInvoiceRepo),
            array('dothiv.payitforward.service.order', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockOrderService),
            array('dothiv.payitforward.mailer.order', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockOrderMailer),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $argumentMap = array(
            array('order', '17'),
            array('invoice', '42'),
            array('recipient', 'john.doe@example.com'),
            array('recipientName', 'John Doe'),
        );
        $this->mockInput->expects($this->any())->method('getArgument')
            ->will($this->returnValueMap($argumentMap));

        $order = new Order();
        $this->mockOrderRepo->expects($this->once())->method('getById')->with('17')
            ->willReturn($order);

        $invoice = new Invoice();
        $this->mockInvoiceRepo->expects($this->once())->method('getById')->with('42')
            ->willReturn($invoice);

        $vouchers = new ArrayCollection();
        $this->mockOrderService->expects($this->once())->method('assignVouchers')
            ->with($order)->willReturn($vouchers);

        $this->mockOrderMailer->expects($this->once())->method('send')
            ->with($order, $invoice, $vouchers, new EmailValue('john.doe@example.com'), 'John Doe');

        $this->assertEquals(0, $this->getTestObject()->run($this->mockInput, $this->mockOutput));
    }

    /**
     * @return SendInvoiceCommand
     */
    protected function getTestObject()
    {
        $command = new SendInvoiceCommand();
        $command->setContainer($this->mockContainer);
        return $command;
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockInput = $this->getMockBuilder('\Symfony\Component\Console\Input\InputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockOutput = $this->getMockBuilder('\Symfony\Component\Console\Output\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockContainer = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockInvoiceRepo  = $this->getMock('\Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface');
        $this->mockOrderRepo    = $this->getMock('\Dothiv\PayitforwardBundle\Repository\OrderRepositoryInterface');
        $this->mockOrderService = $this->getMock('\Dothiv\PayitforwardBundle\Service\OrderServiceInterface');
        $this->mockOrderMailer  = $this->getMock('\Dothiv\PayitforwardBundle\Service\Mailer\OrderMailerInterface');
    }
} 
