<?php

namespace Dothiv\CharityWebsiteBundle\Command;

use Dothiv\CharityWebsiteBundle\Service\SendClickCounterConfigurationServiceInterface;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sends an email with the respective configuration steps required to set up a click-counter for a domain.
 */
class SendClickCounterConfigurationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('charity:clickcounter:send-configuration')
            ->setDescription('Sends an email with the respective configuration steps required to set up a click-counter for a domain.')
            ->addArgument('domain', InputArgument::REQUIRED, 'the .hiv domain');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SendClickCounterConfigurationServiceInterface $service */
        $service = $this->getContainer()->get('dothiv.charity.clickcounter_notification');
        $service->sendConfiguration(new HivDomainValue($input->getArgument('domain')));
    }
}
