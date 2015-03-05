<?php

namespace Dothiv\ShopBundle\Tests\Command;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\ShopBundle\Command\SendInvoiceCommand;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Repository\OrderRepositoryInterface;
use Dothiv\ShopBundle\Service\OrderMailerInterface;
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
     * @var ORderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOrderRepo;

    /**
     * @var OrderMailerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockMailer;

    /**
     * @test
     * @group DothivShopBundle
     * @group Command
     */
    public function itShouldBeInstantable()
    {
        $this->assertInstanceOf('Dothiv\ShopBundle\Command\SendInvoiceCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivShopBundle
     * @group   Command
     * @depends itShouldBeInstantable
     */
    public function itShouldSendMail()
    {
        $containerMap = array(
            array('dothiv.repository.shop_order', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockOrderRepo),
            array('dothiv.shop.mailer.order', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockMailer),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $argumentMap = array(
            array('order', '17'),
            array('recipient', 'john.doe@example.com'),
            array('recipientName', 'John Doe'),
        );
        $this->mockInput->expects($this->any())->method('getArgument')
            ->will($this->returnValueMap($argumentMap));

        $order   = new Order();
        $invoice = new Invoice();
        $order->setInvoice($invoice);
        $this->mockOrderRepo->expects($this->once())->method('getById')->with('17')
            ->willReturn($order);

        $this->mockMailer->expects($this->once())->method('send')
            ->with($order, $invoice, new EmailValue('john.doe@example.com'), 'John Doe');

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

        $this->mockInput     = $this->getMock('\Symfony\Component\Console\Input\InputInterface');
        $this->mockOutput    = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $this->mockContainer = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $this->mockOrderRepo = $this->getMock('\Dothiv\ShopBundle\Repository\OrderRepositoryInterface');
        $this->mockMailer    = $this->getMock('\Dothiv\ShopBundle\Service\OrderMailerInterface');
    }
}
