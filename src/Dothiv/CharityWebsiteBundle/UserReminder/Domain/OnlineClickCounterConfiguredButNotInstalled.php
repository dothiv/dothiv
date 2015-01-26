<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainWhoisRepositoryInterface;
use Dothiv\CharityWebsiteBundle\UserReminder\Domain\AbstractDomainUserReminder;
use Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use Dothiv\HivDomainStatusBundle\Repository\HivDomainCheckRepositoryInterface;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\UserReminderBundle\Service\UserReminderInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

/**
 * This reminder is sent to domains which are online, and a click-counter has been configured, but not installed
 */
class OnlineClickCounterConfiguredButNotInstalled extends AbstractDomainUserReminder implements UserReminderInterface
{

    /**
     * @var string
     */
    protected $after = '-4 weeks';

    /**
     * @var bool
     */
    protected $nonProfit = false;

    /**
     * @param DomainRepositoryInterface         $domainRepo
     * @param HivDomainCheckRepositoryInterface $domainCheckRepo
     * @param DomainWhoisRepositoryInterface    $domainWhoisRepo
     * @param UserReminderRepositoryInterface   $userReminderRepo
     * @param ClockValue                        $clock
     * @param array                             $config
     * @param UserReminderMailer                $mailer
     */
    public function __construct(
        DomainRepositoryInterface $domainRepo,
        HivDomainCheckRepositoryInterface $domainCheckRepo,
        DomainWhoisRepositoryInterface $domainWhoisRepo,
        UserReminderRepositoryInterface $userReminderRepo,
        ClockValue $clock,
        array $config,
        UserReminderMailer $mailer
    )
    {
        parent::__construct($domainWhoisRepo);
        $this->domainRepo       = $domainRepo;
        $this->userReminderRepo = $userReminderRepo;
        $this->domainCheckRepo  = $domainCheckRepo;
        $this->clock            = $clock;
        $this->config           = $config;
        $this->mailer           = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    function send(IdentValue $type)
    {
        // After 4 weeks
        $reminders = new ArrayCollection();
        $filter    = new FilterQuery();
        $filter->setProperty('live', '0');
        $filter->setProperty('clickcounterconfig', '1');
        $filter->setProperty('nonprofit', $this->nonProfit ? '1' : '0');
        $options = new PaginatedQueryOptions();
        $after   = $this->clock->getNow()->modify($this->after);

        do {
            $paginatedResult = $this->domainRepo->getPaginated($options, $filter);
            if ($paginatedResult->getNextPageKey()->isDefined()) {
                $options->setOffsetKey($paginatedResult->getNextPageKey()->get());
            }
            foreach ($paginatedResult->getResult() as $domain) {
                /** @var Domain $domain */
                // Check if not already notified
                if (!$this->userReminderRepo->findByTypeAndItem($type, $domain)->isEmpty()) {
                    // continue;
                }

                if ($this->isNotOnline($domain)) {
                    continue;
                }

                if ($this->clickCounterWasConfiguredAfter($domain, $after)) {
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

    /**
     * @param Domain    $domain
     * @param \DateTime $date
     *
     * @return bool
     */
    protected function clickCounterWasConfiguredAfter(Domain $domain, \DateTime $date)
    {
        return $date->diff($domain->getActiveBanner()->getCreated())->invert < 0;
    }

    /**
     * @param Domain $domain
     *
     * @return bool
     */
    protected function isNotOnline(Domain $domain)
    {
        $domainCheckOptional = $this->domainCheckRepo->findLatestForDomain($domain);
        if ($domainCheckOptional->isEmpty()) {
            return true;
        }
        /** @var HivDomainCheck $domainCheck */
        $domainCheck = $domainCheckOptional->get();
        return $domainCheck->getStatusCode() !== 200;
    }

    protected function notify(Domain $domain)
    {
        $d      = HivDomainValue::create($domain->getName());
        $locale = $this->getLocale($d);
        $data   = [
            'domain'    => $d->toUTF8(),
            'firstname' => $domain->getOwner()->getFirstname(),
            'lastname'  => $domain->getOwner()->getSurname()
        ];

        $this->mailer->send(
            $data,
            new EmailValue($domain->getOwner()->getEmail()),
            $domain->getOwner()->getFirstname() . ' ' . $domain->getOwner()->getSurname(),
            $this->config[$locale]
        );
    }

    /**
     * @param boolean $nonProfit
     *
     * @return self
     */
    public function setNonProfit($nonProfit)
    {
        $this->nonProfit = (boolean)$nonProfit;
        return $this;
    }
}
