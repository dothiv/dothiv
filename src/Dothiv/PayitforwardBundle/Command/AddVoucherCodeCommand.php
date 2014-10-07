<?php

namespace Dothiv\PayitforwardBundle\Command;

use Dothiv\PayitforwardBundle\Entity\Voucher;
use Dothiv\PayitforwardBundle\Repository\VoucherRepositoryInterface;
use Dothiv\ValueObject\IdentValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddVoucherCodeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('payitforward:code:add')
            ->setDescription('Add voucher code')
            ->addArgument('code', InputArgument::REQUIRED, 'The voucher code to add');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var VoucherRepositoryInterface $voucherRepo */
        $voucherRepo = $this->getContainer()->get('dothiv.repository.payitforward.voucher');
        $voucher     = new Voucher();
        $voucher->setCode(new IdentValue($input->getArgument('code')));
        $voucherRepo->persist($voucher)->flush();
    }
}
