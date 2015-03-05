<?php

namespace Dothiv\ShopBundle\Command;

use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Repository\OrderRepositoryInterface;
use Dothiv\ShopBundle\Service\OrderMailerInterface;
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
            ->setName('shop:invoice:send')
            ->setDescription('Send invoice')
            ->addArgument('order', InputArgument::REQUIRED, 'Order ID')
            ->addArgument('recipient', InputArgument::REQUIRED, 'Recipient email')
            ->addArgument('recipientName', InputArgument::REQUIRED, 'Recipient name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var OrderRepositoryInterface $orderRepo */
        $orderRepo = $this->getContainer()->get('dothiv.repository.shop_order');
        /** @var OrderMailerInterface $mailer */
        $mailer = $this->getContainer()->get('dothiv.shop.mailer.order');

        /** @var Order $Order */
        $order = $orderRepo->getById($input->getArgument('order'));
        $mailer->send(
            $order,
            $order->getInvoice()->get(),
            new EmailValue($input->getArgument('recipient')),
            $input->getArgument('recipientName')
        );
    }
}
