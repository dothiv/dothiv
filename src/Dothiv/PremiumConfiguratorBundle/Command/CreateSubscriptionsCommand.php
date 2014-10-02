<?php

namespace Dothiv\PremiumConfiguratorBundle\Command;

use Dothiv\BusinessBundle\Service\Clock;
use Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Service\InvoiceServiceInterface;
use Dothiv\PremiumConfiguratorBundle\Service\Mailer\SubscriptionConfirmedMailer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSubscriptionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('premiumconfigurator:subscriptions:create')
            ->setDescription('Create subscriptions for new checkouts');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SubscriptionRepositoryInterface $subscriptionRepo */
        $subscriptionRepo = $this->getContainer()->get('dothiv.repository.premiumconfigurator.subscription');
        $stripeConfig     = $this->getContainer()->getParameter('dothiv_premium_configurator.stripe');
        /** @var InvoiceServiceInterface $invoiceService */
        $invoiceService = $this->getContainer()->get('dothiv.premiumconfigurator.service.invoice');
        /** @var Clock $clock */
        $clock = $this->getContainer()->get('clock');
        /** @var SubscriptionConfirmedMailer $mailer */
        $mailer = $this->getContainer()->get('dothiv.premiumconfigurator.mailer.subscription_confirmed');

        \Stripe::setApiKey($this->getContainer()->getParameter('stripe_secret_key'));

        foreach ($subscriptionRepo->findInactive() as $subscription) {
            try {
                // TODO: persist invoices for subscription (for follow-up invoices)
                $invoice = $invoiceService->createInvoiceForSubscription($subscription, $clock->getNow());
                $customer = \Stripe_Customer::create(array(
                    'card'  => $subscription->getToken(), // obtained from Stripe.js
                    'plan'  => $invoice->getVatPercent() > 0 ? 'premium-clickcounter-vat' : 'premium-clickcounter-novat',
                    'email' => (string)$subscription->getEmail()
                ));
                $output->writeln(
                    sprintf('Subscribed %s for %s.', $subscription->getEmail(), $subscription->getDomain()->getName())
                );
                $subscription->activate($customer);
                $subscriptionRepo->persist($subscription)->flush();

                $mailer->sendSubscriptionCreatedMail($subscription, $invoice);
            } catch (\Stripe_CardError $e) {
                $output->writeln(
                    sprintf('Failed to subscribe %s for %s.', $subscription->getEmail(), $subscription->getDomain()->getName())
                );
            }
        }
    }
}
