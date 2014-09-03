<?php

namespace Dothiv\BusinessBundle\Report;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\AdminBundle\Model\Report;
use Dothiv\AdminBundle\Model\ReportEvent;
use Dothiv\AdminBundle\Stats\ReporterInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;

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
        $reports = new ArrayCollection();
        $r       = new Report();
        $r->setTitle('Total');
        $r->setResolution(Report::RESOLUTION_TOTAL);
        $reports->set('total', $r);
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
}
