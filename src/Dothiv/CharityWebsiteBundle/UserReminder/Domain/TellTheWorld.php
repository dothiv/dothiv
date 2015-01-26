<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainWhoisRepositoryInterface;
use Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\UserReminderBundle\Service\UserReminderInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

/**
 * This reminders sends the tell the world package for non-profit and for-profit domains.
 */
class TellTheWorld extends AbstractDomainUserReminder implements UserReminderInterface
{

    /**
     * @var bool
     */
    protected $nonProfit = false;

    /**
     * @var array
     */
    private $config;

    /**
     * @param DomainRepositoryInterface       $domainRepo
     * @param DomainWhoisRepositoryInterface  $domainWhoisRepo
     * @param UserReminderRepositoryInterface $userReminderRepo
     * @param ClockValue                      $clock
     * @param array                           $config
     * @param UserReminderMailer              $mailer
     */
    public function __construct(
        DomainRepositoryInterface $domainRepo,
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
        $this->clock            = $clock;
        $this->config           = $config;
        $this->mailer           = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send(IdentValue $type)
    {
        $reminders = new ArrayCollection();
        $filter    = new FilterQuery();
        $filter->setProperty('live', '0', '!=');
        $filter->setProperty('owner', '0', '!=');
        $filter->setProperty('nonprofit', $this->nonProfit ? '1' : '0');
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

    /**
     * @param Domain $domain
     */
    protected function notify(Domain $domain)
    {
        $d      = HivDomainValue::create($domain->getName());
        $owner  = $domain->getOwner();
        $locale = $this->getLocale($d);
        $data   = [
            'domain'     => $d->toUTF8(),
            'firstname'  => $owner->getFirstname(),
            'lastname'   => $owner->getSurname(),
            'claimToken' => $domain->getToken(),
        ];

        $this->mailer->send(
            $data,
            new EmailValue($owner->getEmail()),
            $domain->getOwnerEmail(),
            $this->config['templates'][$locale],
            $this->config['attachments'],
            $locale
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
