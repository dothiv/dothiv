<?php

namespace Dothiv\RegistryWebsiteBundle\Report;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\AdminBundle\Model\Report;
use Dothiv\AdminBundle\Model\ReportEvent;
use Dothiv\AdminBundle\Stats\ReporterInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepositoryInterface;

class NonProfitRegistrationReporter implements ReporterInterface
{
    /**
     * @var NonProfitRegistrationRepositoryInterface
     */
    private $nonProfitRegistrationRepo;

    /**
     * @param NonProfitRegistrationRepositoryInterface $nonProfitRegistrationRepo
     */
    public function __construct(NonProfitRegistrationRepositoryInterface $nonProfitRegistrationRepo)
    {
        $this->nonProfitRegistrationRepo = $nonProfitRegistrationRepo;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Non-Profit Registrations';
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
     * @param string $reportId
     *
     * @return ReportEvent[]|ArrayCollection
     */
    public function getEvents($reportId)
    {
        switch ($reportId) {
            case 'total':
            default:
                return $this->getTotals();
        }
    }

    /**
     * @return ReportEvent[]|ArrayCollection
     */
    protected function getTotals()
    {
        $date  = null;
        $count = 0;
        foreach ($this->nonProfitRegistrationRepo->findAll() as $registration) {
            /** @var NonProfitRegistration $registration */
            if ($date < $registration->getCreated()) {
                $date = $registration->getCreated();
            }
            $count += 1;

        }
        $events = new ArrayCollection();
        $events->add(new ReportEvent($date, $count));
        return $events;
    }
}
