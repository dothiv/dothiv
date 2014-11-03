<?php

namespace Dothiv\PremiumConfiguratorBundle\Tests\Command;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Command\SendInvoiceCommand;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Service\Mailer\SubscriptionConfirmedMailerInterface;
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
     * @var SubscriptionRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockSubscriptionRepo;

    /**
     * @var SubscriptionConfirmedMailerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockSubscriptionConfirmedMailer;

    /**
     * @test
     * @group DothivPremiumConfiguratorBundle
     * @group Command
     */
    public function itShouldBeInstantiateable()
    {
        $this->assertInstanceOf('Dothiv\PremiumConfiguratorBundle\Command\SendInvoiceCommand', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivPremiumConfiguratorBundle
     * @group   Command
     * @depends itShouldBeInstantiateable
     */
    public function itShouldConfigureBanners()
    {
        $containerMap = array(
            array('dothiv.repository.payitforward.subscription', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockSubscriptionRepo),
            array('dothiv.repository.invoice', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockInvoiceRepo),
            array('dothiv.payitforward.mailer.subscription', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mockSubscriptionConfirmedMailer),
        );
        $this->mockContainer->expects($this->any())->method('get')
            ->will($this->returnValueMap($containerMap));

        $argumentMap = array(
            array('subscription', '17'),
            array('invoice', '42'),
            array('recipient', 'john.doe@example.com'),
            array('recipientName', 'John Doe'),
        );
        $this->mockInput->expects($this->any())->method('getArgument')
            ->will($this->returnValueMap($argumentMap));

        $subscription = new Subscription();
        $this->mockSubscriptionRepo->expects($this->once())->method('getById')->with('17')
            ->willReturn($subscription);

        $invoice = new Invoice();
        $this->mockInvoiceRepo->expects($this->once())->method('getById')->with('42')
            ->willReturn($invoice);

        $this->mockSubscriptionConfirmedMailer->expects($this->once())->method('sendSubscriptionCreatedMail')
            ->with($subscription, $invoice, new EmailValue('john.doe@example.com'), 'John Doe');

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

        $this->mockInvoiceRepo
            = $this->getMock('\Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface');
        $this->mockSubscriptionRepo
            = $this->getMock('\Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepositoryInterface');
        $this->mockSubscriptionConfirmedMailer
            = $this->getMock('\Dothiv\PremiumConfiguratorBundle\Service\Mailer\SubscriptionConfirmedMailerInterface');
    }
} 
