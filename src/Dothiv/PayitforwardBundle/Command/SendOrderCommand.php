<?php

namespace Dothiv\PayitforwardBundle\Command;

use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Exception\InsufficientResourcesException;
use Dothiv\PayitforwardBundle\Repository\OrderRepositoryInterface;
use Dothiv\PayitforwardBundle\Service\InvoiceServiceInterface;
use Dothiv\PayitforwardBundle\Service\OrderServiceInterface;
use Dothiv\PayitforwardBundle\Service\Mailer\OrderMailerInterface;
use Dothiv\ValueObject\EmailValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendOrderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('payitforward:order:send')
            ->setDescription('Send emails for orders');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var OrderRepositoryInterface $orderRepo */
        $orderRepo = $this->getContainer()->get('dothiv.repository.payitforward.order');
        /** @var InvoiceServiceInterface $invoiceService */
        $invoiceService = $this->getContainer()->get('dothiv.payitforward.service.invoice');
        /** @var OrderServiceInterface $orderService */
        $orderService = $this->getContainer()->get('dothiv.payitforward.service.order');
        /** @var OrderMailerInterface $mailer */
        $mailer = $this->getContainer()->get('dothiv.payitforward.mailer.order');

        \Stripe::setApiKey($this->getContainer()->getParameter('stripe_secret_key'));

        foreach ($orderRepo->findNew() as $order) {
            /** @var Order $order */
            try {
                $vouchers = $orderService->assignVouchers($order);
            } catch (InsufficientResourcesException $e) {
                $output->writeln('[ERROR] Not enough voucher codes!');
                continue;
            }
            try {
                $invoice = $invoiceService->createInvoice($order);
                $charge  = \Stripe_Charge::create(array(
                    'amount'      => $invoice->getTotalPrice(),
                    'currency'    => 'eur',
                    'card'        => $order->getToken(),
                    'description' => $invoice->getItemDescription()
                ));
                $order->activate($charge);
                $orderRepo->persist($order)->flush();
                $mailer->send($order, $invoice, $vouchers);
                foreach($this->getContainer()->getParameter('dothiv_business.invoice_copy') as $extraRecipient) {
                    $mailer->send($order, $invoice, $vouchers, new EmailValue($extraRecipient['email']), $extraRecipient['name']);
                }
                $output->writeln(
                    sprintf('Processed order by %s.', $order->getEmail())
                );
            } catch (\Exception $e) {
                $output->writeln(
                    sprintf('Failed to process order for %s: %s', $order->getEmail(), $e->getMessage())
                );
            }
        }
    }
}
