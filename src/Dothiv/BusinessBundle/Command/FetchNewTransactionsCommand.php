<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\AfiliasImporterBundle\AfiliasImporterBundleEvents;
use Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent;
use Dothiv\AfiliasImporterBundle\Service\AfiliasImporterServiceInterface;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * A command to fetch new domain transactions and register them in the app
 */
class FetchNewTransactionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:transactions:fetch')
            ->setDescription('Fetch new domain transactions.')
            ->addOption('url', 'u', InputOption::VALUE_OPTIONAL, 'Importer Service URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConfigRepositoryInterface $configRepo */
        /** @var AfiliasImporterServiceInterface $service */
        /** @var Config $config */
        $configRepo = $this->getContainer()->get('dothiv.repository.config');
        $service    = $this->getContainer()->get('dothiv_afilias_importer.service');
        $config     = $configRepo->get('dothiv_afilias_importer.transactions.next_url');

        $url = Option::fromValue($input->getOption('url'))->getOrElse(
            Option::fromValue($config->getValue())->getOrElse($this->getContainer()->getParameter('dothiv_afilias_importer.service_url') . 'transactions')
        );

        if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            /** @var EventDispatcherInterface $dispatcher */
            $dispatcher = $this->getContainer()->get('dothiv.business.event_dispatcher');

            $eventMap = array(
                AfiliasImporterBundleEvents::DOMAIN_CREATED     => 'created',
                AfiliasImporterBundleEvents::DOMAIN_DELETED     => 'deleted',
                AfiliasImporterBundleEvents::DOMAIN_TRANSFERRED => 'transferred',
                AfiliasImporterBundleEvents::DOMAIN_UPDATED     => 'updated',
            );
            foreach ($eventMap as $type => $meaning) {
                $dispatcher->addListener($type, function (DomainTransactionEvent $event) use ($output, $meaning) {
                    $output->writeln(sprintf('Domain %s: %s', $meaning, $event->ObjectName));
                });
            }
        }

        $currentUrl = $url;
        do {
            $nextUrl = $service->fetchTransactions(new URLValue($currentUrl));
        } while ($nextUrl != $currentUrl && $currentUrl = $nextUrl);

        $config->setValue((string)$nextUrl);
        $configRepo->persist($config)->flush();

        // clear mail spool, see http://symfony.com/doc/2.0/cookbook/console/sending_emails.html
        if ($this->getContainer()->getParameter("kernel.environment") != 'test') {
            $this->getContainer()->get('mailer')->getTransport()->getSpool()->flushQueue($this->getContainer()->get('swiftmailer.transport.real'));
        }
    }
}
