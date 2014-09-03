<?php

namespace Dothiv\BusinessBundle\Report;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\AdminBundle\Model\Report;
use Dothiv\AdminBundle\Model\ReportEvent;
use Dothiv\AdminBundle\Stats\ReporterInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\BusinessBundle\Entity\Domain;
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
        $reports            = new ArrayCollection();
        $totalDomainsReport = new Report();
        $totalDomainsReport->setTitle('Total');
        $totalDomainsReport->setResolution(Report::RESOLUTION_TOTAL);
        $reports->set('total', $totalDomainsReport);
        $clickCountersReport = new Report();
        $clickCountersReport->setTitle('Click-Counters');
        $clickCountersReport->setResolution(Report::RESOLUTION_TOTAL);
        $reports->set('clickcounters', $clickCountersReport);
        $clicksReport = new Report();
        $clicksReport->setTitle('Clicks');
        $clicksReport->setResolution(Report::RESOLUTION_TOTAL);
        $reports->set('clicks', $clicksReport);
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
            case 'total':
            default:
                return $this->getTotal();
            case 'clickcounters':
                return $this->getClickCounters();
            case 'clicks':
                return $this->getClicks();
        }
    }

    /**
     * @return ReportEvent[]|ArrayCollection
     */
    protected function getTotal()
    {
        $date  = null;
        $count = 0;
        foreach ($this->domainRepo->findAll() as $domain) {
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

    /**
     * @return ReportEvent[]|ArrayCollection
     */
    protected function getClickCounters()
    {
        $date  = null;
        $count = 0;
        foreach ($this->domainRepo->findAll() as $domain) {
            /** @var Domain $domain */
            if (Option::fromValue($domain->getActiveBanner())->isDefined()) {
                $count += 1;
                if ($domain->getCreated() > $date) {
                    $date = $domain->getCreated();
                }
            }
        }
        $events = new ArrayCollection();
        $events->add(new ReportEvent($date, $count));
        return $events;
    }

    /**
     * @return ReportEvent[]|ArrayCollection
     */
    protected function getClicks()
    {
        $date  = null;
        $count = 0;
        foreach ($this->domainRepo->findAll() as $domain) {
            /** @var Domain $domain */
            $count += $domain->getClickcount();
            if ($domain->getCreated() > $date) {
                $date = $domain->getCreated();
            }
        }
        $events = new ArrayCollection();
        $events->add(new ReportEvent($date, $count));
        return $events;
    }
}
