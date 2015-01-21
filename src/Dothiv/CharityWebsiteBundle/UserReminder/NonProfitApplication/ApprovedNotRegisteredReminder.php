<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\NonProfitApplication;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepositoryInterface;
use Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\UserReminderBundle\Service\UserReminderInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

class ApprovedNotRegisteredReminder extends AbstractNonProfitApplicationReminder implements UserReminderInterface
{

    /**
     * @param NonProfitRegistrationRepositoryInterface $nonProfitRepo
     * @param UserReminderRepositoryInterface          $userReminderRepo
     * @param ClockValue                               $clock
     * @param array                                    $config
     * @param UserReminderMailer                       $mailer
     */
    public function __construct(
        NonProfitRegistrationRepositoryInterface $nonProfitRepo,
        UserReminderRepositoryInterface $userReminderRepo,
        ClockValue $clock,
        array $config,
        UserReminderMailer $mailer
    )
    {
        $this->mailer           = $mailer;
        $this->config           = $config;
        $this->nonProfitRepo    = $nonProfitRepo;
        $this->clockValue       = $clock;
        $this->userReminderRepo = $userReminderRepo;
    }

    /**
     * {@inheritdoc}
     */
    function send(IdentValue $type)
    {
        $reminders = new ArrayCollection();
        $after     = $this->clockValue->getNow()->modify('+6 weeks');
        $options   = new PaginatedQueryOptions();
        $filter    = new FilterQuery();
        $filter->setProperty('older', $after);
        $filter->setProperty('approved', true);

        do {
            $paginatedResult = $this->nonProfitRepo->getPaginated($options, $filter);
            if ($paginatedResult->getNextPageKey()->isDefined()) {
                $options->setOffsetKey($paginatedResult->getNextPageKey()->get());
            }
            foreach ($paginatedResult->getResult() as $nonProfitRegistration) {
                /** @var NonProfitRegistration $nonProfitRegistration */
                // Check if not already notified
                if (!$this->userReminderRepo->findByTypeAndItem($type, $nonProfitRegistration)->isEmpty()) {
                    continue;
                }
                $reminder = new UserReminder();
                $reminder->setType($type);
                $reminder->setIdent($nonProfitRegistration);
                $this->userReminderRepo->persist($reminder);
                $this->notify($nonProfitRegistration);
                $reminders->add($reminder);
            }
        } while ($paginatedResult->getNextPageKey()->isDefined());

        $this->userReminderRepo->flush();
        return $reminders;
    }
}
