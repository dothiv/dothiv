<?php

namespace Dothiv\ShopBundle\Command;

use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Repository\OrderRepositoryInterface;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessOrderCommand extends ContainerAwareCommand
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

        \Stripe::setApiKey($this->getContainer()->getParameter('stripe_secret_key'));

        foreach ($orderRepo->findNew() as $order) {
            /** @var Order $order */
            // TODO: persist order currency
            // TODO: extract pricing service
            /*
            $charge = \Stripe_Charge::create(array(
                'amount'      => $invoice->getTotalPrice(),
                'currency'    => 'eur',
                'card'        => $order->getStripeCard()->toScalar(),
                'description' => $invoice->getItemDescription()
            ));
            $order->setStripeCharge(new IdentValue($charge->id));
            $orderRepo->persist($order)->flush();
            $output->writeln(
                sprintf('Processed order by %s.', $order->getEmail())
            );
            */


        }
    }
}
