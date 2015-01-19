<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\NonProfitDomain;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\UserReminderBundle\Service\UserReminderInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\IdentValue;

/**
 * This reminder is sent to non-profit domains, which are set up and live but have no clicks.
 */
class ClickCounterConfiguredButNoClicksReminder implements UserReminderInterface
{

    /**
     * Domain must have less than this number of clicks
     *
     * @var int
     */
    private $clickThreshold = 10;

    /**
     * @param DomainRepositoryInterface       $domainRepo
     * @param UserReminderRepositoryInterface $userReminderRepo ,
     * @param ClockValue                      $clock
     */
    public function __construct(DomainRepositoryInterface $domainRepo, UserReminderRepositoryInterface $userReminderRepo, ClockValue $clock)
    {
        $this->domainRepo       = $domainRepo;
        $this->userReminderRepo = $userReminderRepo;
        $this->clock            = $clock;
    }

    /**
     * {@inheritdoc}
     */
    function send(IdentValue $type)
    {
        $reminders = new ArrayCollection();
        return $reminders;
        // TODO: Add support for comparison operators in FilterQuery
        // TODO: live must be a timestamp
        $filter = new FilterQuery();
        $filter->setProperty('live', '<' . $this->clock->getNow()->modify('-4 weeks')->getTimestamp());
        $filter->setProperty('clickcount', '<' . $this->clickThreshold);
        $options = new PaginatedQueryOptions();
        foreach ($this->domainRepo->getPaginated($options, $filter)->getResult() as $domain) {
            /** @var Domain $domain */
            // Check if not already notified
            if (!$this->userReminderRepo->findByTypeAndItem($type, $domain)->isEmpty()) {
                continue;
            }
            $reminder = new UserReminder();
            $reminder->setType($type);
            $reminder->setIdent($domain);
            // $this->userReminderRepo->persist($reminder);
            // $this->notify($domain);
            $reminders->add($reminder);
        }
        // $this->userReminderRepo->flush();
        return $reminders;
    }
}
