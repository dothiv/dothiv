<?php

namespace Dothiv\PremiumConfiguratorBundle\Command;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Service\Mailer\SubscriptionConfirmedMailerInterface;
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
            ->setName('premiumconfigurator:invoice:send')
            ->setDescription('Send invoice')
            ->addArgument('subscription', InputArgument::REQUIRED, 'Subscription ID')
            ->addArgument('invoice', InputArgument::REQUIRED, 'Invoice ID')
            ->addArgument('recipient', InputArgument::REQUIRED, 'Recipient email')
            ->addArgument('recipientName', InputArgument::REQUIRED, 'Recipient name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SubscriptionRepositoryInterface $subscriptionRepo */
        $subscriptionRepo = $this->getContainer()->get('dothiv.repository.payitforward.subscription');
        /** @var InvoiceRepositoryInterface $invoiceRepo */
        $invoiceRepo = $this->getContainer()->get('dothiv.repository.invoice');
        /** @var SubscriptionConfirmedMailerInterface $mailer */
        $mailer = $this->getContainer()->get('dothiv.payitforward.mailer.subscription');

        /** @var Subscription $subscription */
        $subscription    = $subscriptionRepo->getById($input->getArgument('subscription'));
        /** @var Invoice $invoice */
        $invoice = $invoiceRepo->getById($input->getArgument('invoice'));
        $mailer->sendSubscriptionCreatedMail(
            $subscription, 
            $invoice, 
            new EmailValue($input->getArgument('recipient')), 
            $input->getArgument('recipientName')
        );
    }
}
