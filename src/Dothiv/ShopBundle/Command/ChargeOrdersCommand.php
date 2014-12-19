<?php

namespace Dothiv\ShopBundle\Command;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Repository\OrderRepositoryInterface;
use Dothiv\ShopBundle\Service\InvoiceServiceInterface;
use Dothiv\ValueObject\IdentValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChargeOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('shop:orders:charge')
            ->setDescription('Charge new orders');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var OrderRepositoryInterface $orderRepo */
        $orderRepo = $this->getContainer()->get('dothiv.repository.shop_order');
        /** @var InvoiceServiceInterface $invoiceService */
        $invoiceService = $this->getContainer()->get('dothiv.shop.invoice');

        \Stripe::setApiKey($this->getContainer()->getParameter('stripe_secret_key'));

        foreach ($orderRepo->findNew() as $order) {
            $invoice = $invoiceService->createInvoice($order);
            /** @var Order $order */
            $charge = \Stripe_Charge::create(array(
                'amount'      => $invoice->getTotalPrice(),
                'currency'    => strtolower($order->getCurrency()->toScalar()),
                'card'        => $order->getStripeToken()->toScalar(),
                'description' => $invoice->getItemDescription()
            ));
            $order->setStripeCharge(new IdentValue($charge->id));
            $orderRepo->persist($order)->flush();
            $output->writeln(
                sprintf('Processed order for %s by %s.', $order->getDomain(), $order->getEmail())
            );
            $this->showOrder($output, $order, $invoice);
        }
    }

    /**
     * @param OutputInterface $output
     * @param Order           $order
     */
    protected function showOrder(OutputInterface $output, Order $order, Invoice $invoice)
    {
        $table = new TableHelper();
        $table->setHeaders(array('Name', 'Value'));
        $table->addRow(array('Domain', $order->getDomain()->toScalar()));
        $table->addRow(array('Price', ($invoice->getTotalPrice() / 100) . ' ' . ($order->getCurrency() == Order::CURRENCY_EUR ? '€' : '$')));
        $table->addRow(array('Name', $order->getFirstname() . ' ' . $order->getLastname()));
        $table->addRow(array('Email', $order->getEmail()));
        $table->addRow(array('Locality', $order->getLocality()));
        if ($order->getLocality2()->isDefined()) {
            $table->addRow(array('Locality (ctd.)', $order->getLocality2()->get()));
        }
        $table->addRow(array('City', $order->getCity()));
        $table->addRow(array('Country', $order->getCountry()));
        if ($order->getOrganization()->isDefined()) {
            $table->addRow(array('Organization', $order->getOrganization()->get()));
        }
        $table->addRow(array('Phone', $order->getPhone()));
        if ($order->getFax()->isDefined()) {
            $table->addRow(array('Fax', $order->getFax()->get()));
        }
        $table->render($output);
    }
}
