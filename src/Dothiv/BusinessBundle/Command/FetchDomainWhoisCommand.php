<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainWhois;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainWhoisRepositoryInterface;
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
class FetchDomainWhoisCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:whois:domains')
            ->setDescription('Fetch WHOIS for all domains');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reportParser    = new WhoisReportParser();
        $domainWhoisRepo = $this->getDomainWhoisRepository();
        foreach ($this->getDomainRepository()->findAll() as $domain) {
            /** @var Domain $domain */
            $d                   = new HivDomainValue($domain->getName());
            $report              = $reportParser->parse($this->getWhois()->lookup($d));
            $domainWhois         = DomainWhois::create($d, $report);
            $domainWhoisOptional = $domainWhoisRepo->findByDomain($d);
            if ($domainWhoisOptional->isDefined()) {
                $domainWhoisRepo->persist($domainWhoisOptional->get()->update($domainWhois));
            } else {
                $domainWhoisRepo->persist($domainWhois);
            }
            $domainWhoisRepo->flush();
        }
    }

    /**
     * @return WhoisServiceInterface
     */
    protected function getWhois()
    {
        return $this->getContainer()->get('whois');
    }

    /**
     * @return DomainRepositoryInterface
     */
    protected function getDomainRepository()
    {
        return $this->getContainer()->get('dothiv.repository.domain');
    }

    /**
     * @return DomainWhoisRepositoryInterface
     */
    protected function getDomainWhoisRepository()
    {
        return $this->getContainer()->get('dothiv.repository.domain_whois');
    }
}
