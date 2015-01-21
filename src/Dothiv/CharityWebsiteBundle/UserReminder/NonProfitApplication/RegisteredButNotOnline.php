<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\NonProfitApplication;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepositoryInterface;
use Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use Dothiv\HivDomainStatusBundle\Repository\HivDomainCheckRepositoryInterface;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\UserReminderBundle\Service\UserReminderInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\IdentValue;

/**
 * See https://trello.com/c/Bq6HTqh2
 */
class RegisteredButNotOnline extends AbstractNonProfitApplicationReminder implements UserReminderInterface
{

    /**
     * @param NonProfitRegistrationRepositoryInterface $nonProfitRepo
     * @param DomainRepositoryInterface                $domainRepo
     * @param HivDomainCheckRepositoryInterface        $domainCheckRepo
     * @param UserReminderRepositoryInterface          $userReminderRepo
     * @param ClockValue                               $clock
     * @param array                                    $config
     * @param UserReminderMailer                       $mailer
     */
    public function __construct(
        NonProfitRegistrationRepositoryInterface $nonProfitRepo,
        DomainRepositoryInterface $domainRepo,
        HivDomainCheckRepositoryInterface $domainCheckRepo,
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
        $this->domainRepo       = $domainRepo;
        $this->domainCheckRepo  = $domainCheckRepo;
    }

    /**
     * {@inheritdoc}
     */
    function send(IdentValue $type)
    {
        $reminders = new ArrayCollection();
        $after     = $this->clockValue->getNow()->modify('-4 weeks');

        $options = new PaginatedQueryOptions();
        $filter  = new FilterQuery();
        $filter->setProperty('approved', true);
        do {
            $paginatedResult = $this->nonProfitRepo->getPaginated($options, $filter);
            if ($paginatedResult->getNextPageKey()->isDefined()) {
                $options->setOffsetKey($paginatedResult->getNextPageKey()->get());
            }
            foreach ($paginatedResult->getResult() as $nonProfitRegistration) {
                // Check if not already notified
                if (!$this->userReminderRepo->findByTypeAndItem($type, $nonProfitRegistration)->isEmpty()) {
                    continue;
                }

                // Find domain for the registration
                $domainOptional = $this->findDomainForRegistration($nonProfitRegistration);
                if ($domainOptional->isEmpty()) {
                    continue;
                }
                /** @var Domain $domain */
                $domain = $domainOptional->get();

                if ($this->domainWasCreatedAfterDate($domain, $after)) {
                    continue;
                }

                if ($this->domainIsResolving($domain)) {
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
    protected function domainIsResolving(Domain $domain)
    {
        $domainCheckOptional = $this->domainCheckRepo->findLatestForDomain($domain);
        if ($domainCheckOptional->isEmpty()) {
            return true; // This is not correct, but no check is present, so assume it is working.
        }
        /** @var HivDomainCheck $domainCheck */
        $domainCheck = $domainCheckOptional->get();
        return $domainCheck->getDnsOk();
    }

    /**
     * Find Domain for this registration
     *
     * @param NonProfitRegistration $nonProfitRegistration
     *
     * @return \PhpOption\Option
     */
    protected function findDomainForRegistration(NonProfitRegistration $nonProfitRegistration)
    {
        $domainOptional = $this->domainRepo->getDomainByName($nonProfitRegistration->getDomain());
        return $domainOptional;
    }
}
