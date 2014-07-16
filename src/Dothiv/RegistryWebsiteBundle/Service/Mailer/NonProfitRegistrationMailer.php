<?php

namespace Dothiv\RegistryWebsiteBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Event\UserTokenEvent;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class NonProfitRegistrationMailer
{
    /**
     * @var ContentMailerInterface
     */
    private $contentMailer;

    /**
     * @param ContentMailerInterface $contentMailer
     */
    public function __construct(
        ContentMailerInterface $contentMailer)
    {
        $this->contentMailer = $contentMailer;
    }

    /**
     * @param NonProfitRegistration $registration
     *
     * @return void
     */
    public function sendReceiptConfirmation(NonProfitRegistration $registration)
    {
        $data = array(
            'domain'          => $registration->getDomainUTF8(),
            'personSurname'   => $registration->getPersonSurname(),
            'personFirstname' => $registration->getPersonFirstname(),
        );
        $this->contentMailer->sendContentTemplateMail(
            'nonprofit.registration.received',
            'en',
            $registration->getPersonEmail(),
            $registration->getPersonFirstname() . ' ' . $registration->getPersonSurname(),
            $data
        );
    }
}
