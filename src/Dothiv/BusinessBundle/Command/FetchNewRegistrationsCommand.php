<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\AfiliasImporterBundle\AfiliasImporterBundleEvents;
use Dothiv\AfiliasImporterBundle\Event\DomainRegisteredEvent;
use Dothiv\AfiliasImporterBundle\Service\AfiliasImporterServiceInterface;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\BusinessBundle\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * A command to fetch new domain registrations and register the in the app
 *
 * @author Markus Tacker <m@dotHIV.org>
 */
class FetchNewRegistrationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:registrations:fetch')
            ->setDescription('Fetch new domain registrations.')
            ->addOption('url', 'u', InputOption::VALUE_OPTIONAL, 'Importer Service URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConfigRepositoryInterface $configRepo */
        /** @var AfiliasImporterServiceInterface $service */
        /** @var Config $config */
        $configRepo = $this->getContainer()->get('dothiv.repository.config');
        $service    = $this->getContainer()->get('dothiv_afilias_importer.service');
        $config     = $configRepo->get('dothiv_afilias_importer.next_url');

        $url = Option::fromValue($input->getOption('url'))->getOrElse(
            Option::fromValue($config->getValue())->getOrElse($this->getContainer()->getParameter('dothiv_afilias_importer.service_url') . 'registrations')
        );

        if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            /** @var EventDispatcherInterface $dispatcher */
            $dispatcher = $this->getContainer()->get('dothiv.business.event_dispatcher');
            $dispatcher->addListener(AfiliasImporterBundleEvents::DOMAIN_REGISTERED, function (DomainRegisteredEvent $event) use ($output) {
                $output->writeln(sprintf('New registration: %s', $event->DomainName));
            });
        }

        $nextUrl = $service->fetchRegistrations(new URLValue($url));
        $config->setValue((string)$nextUrl);
        $configRepo->persist($config)->flush();

        // clear mail spool, see http://symfony.com/doc/2.0/cookbook/console/sending_emails.html
        if ($this->getContainer()->getParameter("kernel.environment") != 'test') {
            $this->getContainer()->get('mailer')->getTransport()->getSpool()->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
        }
    }
}
