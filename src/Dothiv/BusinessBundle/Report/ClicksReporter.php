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

class ClicksReporter implements ReporterInterface
{

    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepo;

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @param ConfigRepositoryInterface $configRepo
     */
    public function __construct(ConfigRepositoryInterface $configRepo, DomainRepositoryInterface $domainRepo)
    {
        $this->configRepo = $configRepo;
        $this->domainRepo = $domainRepo;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return "Clicks";
    }

    /**
     * @return Report[]|ArrayCollection
     */
    public function getReports()
    {
        $reports      = new ArrayCollection();
        $clicksReport = new Report();
        $clicksReport->setTitle('Total');
        $clicksReport->setResolution(Report::RESOLUTION_TOTAL);
        $reports->set('clicks', $clicksReport);
        $clickCountersReport = new Report();
        $clickCountersReport->setTitle('Counting Domains');
        $clickCountersReport->setResolution(Report::RESOLUTION_TOTAL);
        $reports->set('clickcounters', $clickCountersReport);
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
            case 'clicks':
                return $this->getClicks();
            case 'clickcounters':
                return $this->getClickCounters();
        }
    }

    /**
     * @return ReportEvent[]|ArrayCollection
     */
    protected function getClicks()
    {
        $events = new ArrayCollection();
        $events->add(new ReportEvent(new \DateTime(), intval($this->configRepo->get('clickcount')->getValue())));
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
            if (Option::fromValue($domain->getActiveBanner())->isDefined() && $domain->getClickcount() > 0) {
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
}
