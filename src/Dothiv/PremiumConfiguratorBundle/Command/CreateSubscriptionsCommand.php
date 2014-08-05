<?php

namespace Dothiv\PremiumConfiguratorBundle\Command;

use Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Service\Mailer\SubscriptionConfirmedMailer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        /** @var SubscriptionConfirmedMailer $mailer */
        $mailer = $this->getContainer()->get('dothiv.premiumconfigurator.mailer.subscription_confirmed');

        \Stripe::setApiKey($this->getContainer()->getParameter('stripe_secret_key'));

        foreach ($subscriptionRepo->findInactive() as $subscription) {
            try {
                $customer = \Stripe_Customer::create(array(
                    'card'  => $subscription->getToken(), // obtained from Stripe.js
                    'plan'  => $stripeConfig['plan'],
                    'email' => (string)$subscription->getEmail()
                ));
                $output->writeln(
                    sprintf('Subscribed %s for %s.', $subscription->getEmail(), $subscription->getDomain()->getName())
                );
                $subscription->activate($customer);
                $subscriptionRepo->persist($subscription)->flush();
                $mailer->sendSubscriptionCreatedMail($subscription);
            } catch (\Stripe_CardError $e) {
                $output->writeln(
                    sprintf('Failed to subscribe %s for %s.', $subscription->getEmail(), $subscription->getDomain()->getName())
                );
            }
        }
    }
}
