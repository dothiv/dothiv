<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainWhoisRepositoryInterface;
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
 * This reminder is sent to domains, which are online, but no click-counter has been configured.
 */
class OnlineButClickCounterNotConfigured extends AbstractDomainUserReminder implements UserReminderInterface
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
     * @param DomainWhoisRepositoryInterface    $domainWhoisRepo
     * @param HivDomainCheckRepositoryInterface $domainCheckRepo
     * @param UserReminderRepositoryInterface   $userReminderRepo
     * @param ClockValue                        $clock
     * @param array                             $config
     * @param UserReminderMailer                $mailer
     */
    public function __construct(
        DomainRepositoryInterface $domainRepo,
        DomainWhoisRepositoryInterface $domainWhoisRepo,
        HivDomainCheckRepositoryInterface $domainCheckRepo,
        UserReminderRepositoryInterface $userReminderRepo,
        ClockValue $clock,
        array $config,
        UserReminderMailer $mailer
    )
    {
        parent::__construct($domainWhoisRepo);
        $this->domainRepo       = $domainRepo;
        $this->domainCheckRepo  = $domainCheckRepo;
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
        $filter->setProperty('live', '0');
        $filter->setProperty('clickcounterconfig', '0');
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
                    continue;
                }

                if ($this->domainWasCreatedAfterDate($domain, $after)) {
                    continue;
                }

                if ($this->domainIsNotResolving($domain)) {
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
    protected function domainWasCreatedAfterDate(Domain $domain, \DateTime $date)
    {
        return $date->diff($domain->getCreated())->invert < 0;
    }

    /**
     * @param Domain $domain
     *
     * @return bool
     */
    protected function domainIsNotResolving(Domain $domain)
    {
        $checkOptional = $this->domainCheckRepo->findLatestForDomain($domain);
        if ($checkOptional->isEmpty()) {
            return true; // Assume it is not resolving, wait for check
        }
        /** @var HivDomainCheck $check */
        $check = $checkOptional->get();
        return !$check->getDnsOk();
    }

    /**
     * @param Domain $domain
     */
    protected function notify(Domain $domain)
    {
        $d      = HivDomainValue::create($domain->getName());
        $locale = $this->getLocale($d);
        $data   = [
            'domain'     => $d->toUTF8(),
            'fullname'   => $domain->getOwnerName(),
            'claimToken' => $domain->getToken(),
        ];

        $this->mailer->send(
            $data,
            new EmailValue($domain->getOwnerEmail()),
            $domain->getOwnerEmail(),
            $this->config[$locale]
        );
    }

    /**
     * @param string $after
     *
     * @return self
     */
    public function setAfter($after)
    {
        $this->after = $after;
        return $this;
    }

    /**
     * @param boolean $nonProfit
     *
     * @return self
     */
    public function setNonProfit($nonProfit)
    {
        $this->nonProfit = (bool)$nonProfit;
        return $this;
    }
}
