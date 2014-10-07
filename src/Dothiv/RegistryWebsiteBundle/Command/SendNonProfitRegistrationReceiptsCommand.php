<?php

namespace Dothiv\RegistryWebsiteBundle\Command;

use Dothiv\ValueObject\ClockValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendNonProfitRegistrationReceiptsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('registry:nonprofit:confirm-receipt')
            ->setDescription('Send receipt confirmation mails');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $nprRepo \Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepositoryInterface */
        /** @var $mailer \Dothiv\RegistryWebsiteBundle\Service\Mailer\NonProfitRegistrationMailer */
        /** @var $clock ClockValue */
        $nprRepo = $this->getContainer()->get('dothiv.repository.nonprofitregistration');
        $mailer  = $this->getContainer()->get('dothiv.registry.service.mailer.nonprofitregistration');
        $clock   = $this->getContainer()->get('clock');
        foreach ($nprRepo->getUnconfirmed() as $registration) {
            $output->writeln($registration->getDomainUTF8());
            $mailer->sendReceiptConfirmation($registration);
            $registration->setReceiptSent($clock->getNow());
            $nprRepo->persist($registration)->flush();
        }

        $this->getContainer()->get('mailer')->getTransport()->getSpool()->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
    }
} 
