<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\NonProfitApplication;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepositoryInterface;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\UserReminderBundle\SendWithUs\TemplateRenderer;
use Dothiv\UserReminderBundle\Service\UserReminderInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;

class ApprovedNotRegisteredReminder implements UserReminderInterface
{
    /**
     * @param \Swift_Mailer                            $mailer
     * @param NonProfitRegistrationRepositoryInterface $nonProfitRepo
     * @param UserReminderRepositoryInterface          $userReminderRepo
     * @param TemplateRenderer                         $renderer
     * @param ClockValue                               $clock
     * @param array                                    $config
     * @param string                                   $emailFromAddress
     * @param string                                   $emailFromName
     */
    public function __construct(
        \Swift_Mailer $mailer,
        NonProfitRegistrationRepositoryInterface $nonProfitRepo,
        UserReminderRepositoryInterface $userReminderRepo,
        TemplateRenderer $renderer,
        ClockValue $clock,
        array $config,
        $emailFromAddress,
        $emailFromName
    )
    {
        $this->mailer           = $mailer;
        $this->config           = $config;
        $this->nonProfitRepo    = $nonProfitRepo;
        $this->renderer         = $renderer;
        $this->clockValue       = $clock;
        $this->userReminderRepo = $userReminderRepo;
        $this->emailFromAddress = $emailFromAddress;
        $this->emailFromName    = $emailFromName;
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
        foreach ($this->nonProfitRepo->getPaginated($options, $filter)->getResult() as $nonProfitRegistration) {
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

        $message = \Swift_Message::newInstance();
        $message
            ->setFrom($this->emailFromAddress, $this->emailFromName)
            ->setTo($nonProfitRegistration->getPersonEmail(), $nonProfitRegistration->getPersonFirstname() . ' ' . $nonProfitRegistration->getPersonSurname());

        list($templateId, $versionId) = $this->config[$locale];
        $this->renderer->render($message, $data, $templateId, $versionId);

        $this->mailer->send($message);
    }
}
