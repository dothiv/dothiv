<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Service\WhoisReportParser;
use Dothiv\BusinessBundle\Service\WhoisServiceInterface;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Returns WHOIS information for the given domain
 */
class WhoisCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:whois')
            ->setDescription('Returns WHOIS information for the given domain.')
            ->addArgument('domain', InputArgument::REQUIRED, '.hiv Domain');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain       = $input->getArgument('domain');
        $reportParser = new WhoisReportParser();
        $report       = $reportParser->parse($this->getWhois()->lookup(new HivDomainValue($domain)));

        $table = new TableHelper();
        $table->setHeaders(array('Registrant', 'Organization', 'Registrar'));
        $table->addRow(array(
            $report->get('Registrant Name') . ' <' . $report->get('Registrant Email') . '>',
            $report->get('Registrant Organization'),
            $report->get('Sponsoring Registrar')
        ));
        $table->render($output);
    }

    /**
     * @return WhoisServiceInterface
     */
    protected function getWhois()
    {
        return $this->getContainer()->get('whois');
    }
}
