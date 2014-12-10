<?php

namespace Dothiv\HivDomainStatusBundle\Command;

use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\HivDomainStatusBundle\Event\DomainCheckEvent;
use Dothiv\HivDomainStatusBundle\HivDomainStatusEvents;
use Dothiv\HivDomainStatusBundle\Service\HivDomainStatusServiceInterface;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This command fetches the domain check stati from the service
 */
class FetchHivDomainStatusCommand extends ContainerAwareCommand
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
        /** @var ConfigRepositoryInterface $configRepo */
        /** @var Config $config */
        $service    = $this->getContainer()->get('dothiv_hiv_domain_status.service');
        $configRepo = $this->getContainer()->get('dothiv.repository.config');
        $config     = $configRepo->get('dothiv_hiv_domain_status.check.next_url');

        if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            /** @var EventDispatcherInterface $dispatcher */
            $dispatcher = $this->getContainer()->get('dothiv.business.event_dispatcher');
            $dispatcher->addListener(HivDomainStatusEvents::DOMAIN_CHECK, function (DomainCheckEvent $event) use ($output) {
                $output->writeln(sprintf('%s: %s', $event->getCheck()->domain, $event->getCheck()->valid ? 'OK' : 'ERROR'));
            });
        }

        if (Option::fromValue($config->getValue())->isEmpty()) {
            $nextUrl = $service->fetchChecks();
        } else {
            $nextUrl = $service->fetchChecks(new URLValue($config->getValue()));
        }
        $config->setValue((string)$nextUrl);
        $configRepo->persist($config)->flush();
    }
}
