<?php

namespace Dothiv\BusinessBundle\Report;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\AdminBundle\Model\Report;
use Dothiv\AdminBundle\Model\ReportEvent;
use Dothiv\AdminBundle\Stats\ReporterInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use PhpOption\Option;

class DomainReporter implements ReporterInterface
{

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @param DomainRepositoryInterface $domainRepo
     */
    public function __construct(DomainRepositoryInterface $domainRepo)
    {
        $this->domainRepo = $domainRepo;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return "Registered domains";
    }

    /**
     * @return Report[]|ArrayCollection
     */
    public function getReports()
    {
        $reports                = new ArrayCollection();
        $forProfitDomainsReport = new Report();
        $forProfitDomainsReport->setTitle('For-profit');
        $forProfitDomainsReport->setResolution(Report::RESOLUTION_TOTAL);
        $reports->set('for-profit', $forProfitDomainsReport);
        $nonProfitDomainsReport = new Report();
        $nonProfitDomainsReport->setTitle('Non-profit');
        $nonProfitDomainsReport->setResolution(Report::RESOLUTION_TOTAL);
        $reports->set('non-profit', $nonProfitDomainsReport);
        return $reports;
    }

    /**
     * @param string $id
     *
     * @return ReportEvent[]|ArrayCollection
     */
    public function getEvents($id)
    {
        switch ($id) {
            case 'for-profit':
                return $this->getDomains(function (Domain $domain) {
                    return $domain->getNonprofit() === false;
                });
            case 'non-profit':
                return $this->getDomains(function (Domain $domain) {
                    return $domain->getNonprofit() !== false;
                });
        }
    }

    /**
     * @param \callable $filter
     *
     * @return ReportEvent[]|ArrayCollection
     */
    protected function getDomains($filter)
    {
        $date  = null;
        $count = 0;
        foreach ($this->domainRepo->findAll() as $domain) {
            if (!$filter($domain)) {
                continue;
            }
            /** @var Domain $domain */
            if ($domain->getCreated() > $date) {
                $date = $domain->getCreated();
            }
            $count += 1;

        }
        $events = new ArrayCollection();
        $events->add(new ReportEvent($date, $count));
        return $events;
    }
}
