<?php

namespace Dothiv\PremiumConfiguratorBundle\Service\Mailer;

use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BaseWebsiteBundle\Service\MoneyFormatServiceInterface;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Service\VatRulesInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\ValueObject\EmailValue;
use PhpOption\Option;

class SubscriptionConfirmedMailer implements SubscriptionConfirmedMailerInterface
{

    /**
     * @var ContentMailerInterface
     */
    private $contentMailer;

    /**
     * @var MoneyFormatServiceInterface
     */
    private $moneyFormat;

    /**
     * @var VatRulesInterface
     */
    private $vatRules;

    /**
     * @param ContentMailerInterface      $contentMailer
     * @param MoneyFormatServiceInterface $moneyFormatService
     * @param VatRulesInterface           $vatRules
     */
    public function __construct(
        ContentMailerInterface $contentMailer,
        MoneyFormatServiceInterface $moneyFormatService,
        VatRulesInterface $vatRules
    )
    {
        $this->contentMailer = $contentMailer;
        $this->moneyFormat   = $moneyFormatService;
        $this->vatRules      = $vatRules;
    }

    /**
     * @param Subscription    $subscription
     * @param Invoice         $invoice
     * @param EmailValue|null $recipient
     * @param string|null     $recipientName
     *
     * @return void
     */
    public function sendSubscriptionCreatedMail(Subscription $subscription, Invoice $invoice, EmailValue $recipient = null, $recipientName = null)
    {
        $locale     = 'en';
        $dateFormat = 'M. jS Y'; // de: 'd.m.Y'
        $rules      = $this->vatRules->getRules(
            $invoice->getOrganization()->isDefined(),
            $invoice->getCountry(),
            $invoice->getVatNo()->isDefined()
        );
        $data       = array(
            'firstname' => $subscription->getUser()->getFirstname(),
            'surname'   => $subscription->getUser()->getSurname(),
            'domain'    => $subscription->getDomain()->getName(),
            'invoice'   => array(
                'no'                       => $invoice->getNo(),
                'created'                  => $invoice->getCreated()->format($dateFormat),
                'fullname'                 => $invoice->getFullname(),
                'address1'                 => $invoice->getAddress1(),
                'address2'                 => $invoice->getAddress2(),
                'country'                  => $invoice->getCountry(),
                'organization'             => $invoice->getOrganization()->getOrElse(null),
                'vatNo'                    => $invoice->getVatNo()->getOrElse(null),
                'item_description'         => $invoice->getItemDescription(),
                'item_price'               => $this->moneyFormat->format($invoice->getItemPrice() / 100, $locale),
                'vat_percent'              => $invoice->getVatPercent(),
                'vat_price'                => $this->moneyFormat->format($invoice->getVatPrice() / 100, $locale),
                'total_price'              => $this->moneyFormat->format($invoice->getTotalPrice() / 100, $locale),
                'show_reverse_charge_note' => $rules->showReverseChargeNote()
            )
        );

        $recipientName = Option::fromValue($recipientName)->getOrElse($subscription->getUser()->getFirstname() . ' ' . $subscription->getUser()->getSurname());
        $recipient     = Option::fromValue($recipient)->getOrElse($subscription->getEmail());

        $this->contentMailer->sendContentTemplateMail('premium.purchased', $locale, (string)$recipient, $recipientName, $data);
    }
}
