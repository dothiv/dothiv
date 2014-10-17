<?php

namespace Dothiv\CharityWebsiteBundle\Command;

use Dothiv\CharityWebsiteBundle\Service\SendClickCounterConfigurationServiceInterface;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Finds unconfigured domains and sends the click-counter configuration to them.
 */
class SendClickCounterConfigurationToUnconfiguredCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('charity:clickcounter:notify-uninstalled')
            ->setDescription('Finds unconfigured domains and sends the click-counter configuration to them. ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SendClickCounterConfigurationServiceInterface $service */
        $service = $this->getContainer()->get('dothiv.charity.clickcounter_notification');
        foreach ($service->findDomainsToBeNotified() as $domain) {
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                $output->writeln(
                    sprintf(
                        'Notifying %s for %s',
                        $domain->getOwnerEmail(),
                        HivDomainValue::create($domain->getName())->toUTF8()
                    )
                );
            }
            $command = $this->getApplication()->find('charity:clickcounter:send-configuration');
            $input   = new ArrayInput(array(
                'command' => 'charity:clickcounter:send-configuration',
                'domain'  => $domain->getName()
            ));

            $command->run($input, $output);
        }
    }
}
