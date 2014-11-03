<?php

namespace Dothiv\PayitforwardBundle\Command;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Repository\OrderRepositoryInterface;
use Dothiv\PayitforwardBundle\Service\Mailer\OrderMailerInterface;
use Dothiv\PayitforwardBundle\Service\OrderServiceInterface;
use Dothiv\ValueObject\EmailValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendInvoiceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('payitforward:invoice:send')
            ->setDescription('Send invoices')
            ->addArgument('order', InputArgument::REQUIRED, 'Order ID')
            ->addArgument('invoice', InputArgument::REQUIRED, 'Invoice ID')
            ->addArgument('recipient', InputArgument::REQUIRED, 'Recipient email')
            ->addArgument('recipientName', InputArgument::REQUIRED, 'Recipient name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var OrderRepositoryInterface $orderRepo */
        $orderRepo = $this->getContainer()->get('dothiv.repository.payitforward.order');
        /** @var InvoiceRepositoryInterface $invoiceRepo */
        $invoiceRepo = $this->getContainer()->get('dothiv.repository.invoice');
        /** @var OrderServiceInterface $orderService */
        $orderService = $this->getContainer()->get('dothiv.payitforward.service.order');
        /** @var OrderMailerInterface $mailer */
        $mailer = $this->getContainer()->get('dothiv.payitforward.mailer.order');

        /** @var Order $order */
        $order    = $orderRepo->getById($input->getArgument('order'));
        $vouchers = $orderService->assignVouchers($order);
        /** @var Invoice $invoice */
        $invoice = $invoiceRepo->getById($input->getArgument('invoice'));
        $mailer->send(
            $order, 
            $invoice, 
            $vouchers, 
            new EmailValue($input->getArgument('recipient')), 
            $input->getArgument('recipientName')
        );
    }
}
