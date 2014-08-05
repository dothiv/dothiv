<?php

namespace Dothiv\PremiumConfiguratorBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;

class SubscriptionConfirmedMailer
{
    /**
     * @var ContentMailerInterface
     */
    private $contentMailer;

    /**
     * @param ContentMailerInterface $contentMailer
     */
    public function __construct(
        ContentMailerInterface $contentMailer
    )
    {
        $this->contentMailer = $contentMailer;
    }

    /**
     * @param Subscription $subscription
     *
     * @return void
     */
    public function sendSubscriptionCreatedMail(Subscription $subscription)
    {
        $data = array(
            'firstname' => $subscription->getUser()->getFirstname(),
            'surname'   => $subscription->getUser()->getSurname(),
            'domain'    => $subscription->getDomain()->getName()
        );

        $name = $subscription->getUser()->getFirstname() . ' ' . $subscription->getUser()->getSurname();

        $this->contentMailer->sendContentTemplateMail('premium.purchased', 'en', (string)$subscription->getEmail(), $name, $data);
    }
} 
