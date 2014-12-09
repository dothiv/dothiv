<?php

namespace Dothiv\HivDomainStatusBundle\Command;

use Dothiv\HivDomainStatusBundle\Event\HivDomainStatusEvent;
use Dothiv\HivDomainStatusBundle\HivDomainStatusEvents;
use Dothiv\HivDomainStatusBundle\Service\HivDomainStatusServiceInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This command fetches the domain check stati from the service
 */
class FetchDomainsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:domainstatus:fetch')
            ->setDescription('Fetch hiv domain stati.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var HivDomainStatusServiceInterface $service */
        $service = $this->getContainer()->get('dothiv_hiv_domain_status.service');

        if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            /** @var EventDispatcherInterface $dispatcher */
            $dispatcher = $this->getContainer()->get('dothiv.business.event_dispatcher');
            $dispatcher->addListener(HivDomainStatusEvents::DOMAIN_FETCHED, function (HivDomainStatusEvent $event) use ($output) {
                $output->writeln(sprintf('%s: %s', $event->getDomain()->name, $event->getDomain()->valid ? 'OK' : 'ERROR'));
            });
        }

        $service->fetchDomains();
    }
}
