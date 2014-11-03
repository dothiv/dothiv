<?php


namespace Dothiv\PremiumConfiguratorBundle\Service\Mailer;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\ValueObject\EmailValue;

interface SubscriptionConfirmedMailerInterface
{
    /**
     * @param Subscription    $subscription
     * @param Invoice         $invoice
     * @param EmailValue|null $recipient
     * @param string|null     $recipientName
     *
     * @return void
     */
    public function sendSubscriptionCreatedMail(Subscription $subscription, Invoice $invoice, EmailValue $recipient = null, $recipientName = null);
} 
