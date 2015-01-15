<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\NonProfitApplication;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepositoryInterface;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Mailer\TemplateMailerInterface;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\UserReminderBundle\SendWithUs\TemplateRenderer;
use Dothiv\UserReminderBundle\Service\UserReminderInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

class ApprovedNotRegisteredReminder implements UserReminderInterface
{
    /**
     * @param NonProfitRegistrationRepositoryInterface $nonProfitRepo
     * @param DomainRepositoryInterface                $domainRepo
     * @param UserReminderRepositoryInterface          $userReminderRepo
     * @param ClockValue                               $clock
     * @param TemplateMailerInterface                  $mailer
     * @param array                                    $config
     */
    public function __construct(
        NonProfitRegistrationRepositoryInterface $nonProfitRepo,
        DomainRepositoryInterface $domainRepo,
        UserReminderRepositoryInterface $userReminderRepo,
        TemplateMailerInterface $mailer,
        ClockValue $clock,
        array $config
    )
    {
        $this->mailer           = $mailer;
        $this->config           = $config;
        $this->nonProfitRepo    = $nonProfitRepo;
        $this->clockValue       = $clock;
        $this->userReminderRepo = $userReminderRepo;
        $this->domainRepo       = $domainRepo;
    }

    /**
     * {@inheritdoc}
     */
    function send(IdentValue $type)
    {
        $reminders = new ArrayCollection();
        $after     = $this->clockValue->getNow()->modify('+6 weeks');
        $filter    = new FilterQuery();
        $filter->setProperty('older', $after);
        $filter->setProperty('approved', true);
        $options = new PaginatedQueryOptions();
        $options->setSortField(new IdentValue('approved'));

        do {
            $paginatedResult = $this->nonProfitRepo->getPaginated($options, $filter);
            if ($paginatedResult->getNextPageKey()->isDefined()) {
                $options->setOffsetKey($paginatedResult->getNextPageKey()->get());
            }
            foreach ($paginatedResult->getResult() as $nonProfitRegistration) {
                /** @var NonProfitRegistration $nonProfitRegistration */
                // Check if domain is registered
                $domainOptional = $this->domainRepo->getDomainByName($nonProfitRegistration->getDomain());
                if ($domainOptional->isDefined()) {
                    continue;
                }
                // Check if not already notified
                if (!$this->userReminderRepo->findByTypeAndItem($type, $nonProfitRegistration)->isEmpty()) {
                    continue;
                }
                $reminder = new UserReminder();
                $reminder->setType($type);
                $reminder->setIdent($nonProfitRegistration);
                $this->notify($nonProfitRegistration);
                $this->userReminderRepo->persist($reminder);
                $reminders->add($reminder);
            }
        } while ($paginatedResult->getNextPageKey()->isDefined());
        $this->userReminderRepo->flush();
        return $reminders;
    }

    protected function notify(NonProfitRegistration $nonProfitRegistration)
    {
        $deCountries = array(
            'Deutschland',
            'Österreich',
            'Schweiz'
        );
        $locale      = 'en';
        foreach ($deCountries as $c) {
            if (stristr($nonProfitRegistration->getCountry(), $c) !== false) {
                $locale = 'de';
            }
        }
        $data = [
            'domain'       => HivDomainValue::create($nonProfitRegistration->getDomain())->toUTF8(),
            'firstname'    => $nonProfitRegistration->getPersonFirstname(),
            'lastname'     => $nonProfitRegistration->getPersonSurname(),
            'organization' => $nonProfitRegistration->getOrganization()
        ];

        $this->mailer->send(
            new EmailValue($nonProfitRegistration->getPersonEmail()),
            $nonProfitRegistration->getPersonFirstname() . ' ' . $nonProfitRegistration->getPersonSurname(),
            $this->config[$locale],
            $locale,
            $data
        );
    }
}
