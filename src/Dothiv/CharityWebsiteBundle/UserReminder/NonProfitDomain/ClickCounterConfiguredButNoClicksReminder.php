<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\NonProfitDomain;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\UserReminderBundle\Service\UserReminderInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
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
     * @param UserReminderRepositoryInterface $userReminderRepo
     * @param ClockValue                      $clock
     * @param array                           $config
     * @param UserReminderMailer              $mailer
     */
    public function __construct(
        DomainRepositoryInterface $domainRepo,
        UserReminderRepositoryInterface $userReminderRepo,
        ClockValue $clock,
        array $config,
        UserReminderMailer $mailer
    )
    {
        $this->domainRepo       = $domainRepo;
        $this->userReminderRepo = $userReminderRepo;
        $this->clock            = $clock;
        $this->config           = $config;
        $this->mailer           = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    function send(IdentValue $type)
    {
        $reminders = new ArrayCollection();
        $filter    = new FilterQuery();
        $filter->setProperty('live', $this->clock->getNow()->modify('-4 weeks'), '<');
        $filter->setProperty('clickcount', $this->clickThreshold, '<');
        $filter->setProperty('nonprofit', 1);
        $options = new PaginatedQueryOptions();

        do {
            $paginatedResult = $this->domainRepo->getPaginated($options, $filter);
            if ($paginatedResult->getNextPageKey()->isDefined()) {
                $options->setOffsetKey($paginatedResult->getNextPageKey()->get());
            }
            foreach ($paginatedResult->getResult() as $domain) {
                /** @var Domain $domain */
                // Check if not already notified
                if (!$this->userReminderRepo->findByTypeAndItem($type, $domain)->isEmpty()) {
                    continue;
                }
                $reminder = new UserReminder();
                $reminder->setType($type);
                $reminder->setIdent($domain);
                $this->userReminderRepo->persist($reminder);
                $this->notify($domain);
                $reminders->add($reminder);
            }
        } while ($paginatedResult->getNextPageKey()->isDefined());

        $this->userReminderRepo->flush();
        return $reminders;
    }

    protected function notify(Domain $domain)
    {
        // TODO: detect locale …
        $locale = 'en';
        $data   = [
            'domain'    => HivDomainValue::create($domain->getName())->toUTF8(),
            'firstname' => $domain->getOwner()->getFirstname(),
            'lastname'  => $domain->getOwner()->getSurname()
        ];

        list($templateId, $versionId) = $this->config[$locale];
        $this->mailer->send(
            $data,
            new EmailValue($domain->getOwner()->getEmail()),
            $domain->getOwner()->getFirstname() . ' ' . $domain->getOwner()->getSurname(),
            $templateId, $versionId
        );
    }
}
