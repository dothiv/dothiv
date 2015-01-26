<?php


namespace Dothiv\CharityWebsiteBundle\UserReminder\NonProfitApplication;

use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;

class AbstractNonProfitApplicationReminder
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var UserReminderMailer $mailer
     */
    protected $mailer;

    /**
     * @param NonProfitRegistration $nonProfitRegistration
     */
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
            $data,
            new EmailValue($nonProfitRegistration->getPersonEmail()),
            $nonProfitRegistration->getPersonFirstname() . ' ' . $nonProfitRegistration->getPersonSurname(),
            $this->config[$locale]
        );
    }
}
