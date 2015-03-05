<?php

namespace Dothiv\ShopBundle\Service;

use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BaseWebsiteBundle\Service\MoneyFormatServiceInterface;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Model\CountryModel;
use Dothiv\BusinessBundle\Repository\CountryRepositoryInterface;
use Dothiv\BusinessBundle\Service\VatRulesInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;

class OrderMailer implements OrderMailerInterface
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
     * @var CountryRepositoryInterface
     */
    private $countryRepo;

    /**
     * @param ContentMailerInterface      $contentMailer
     * @param MoneyFormatServiceInterface $moneyFormatService
     * @param VatRulesInterface           $vatRules
     * @param CountryRepositoryInterface  $countryRepo
     */
    public function __construct(
        ContentMailerInterface $contentMailer,
        MoneyFormatServiceInterface $moneyFormatService,
        VatRulesInterface $vatRules,
        CountryRepositoryInterface $countryRepo
    )
    {
        $this->contentMailer = $contentMailer;
        $this->moneyFormat   = $moneyFormatService;
        $this->vatRules      = $vatRules;
        $this->countryRepo   = $countryRepo;
    }

    /**
     * @param Order           $order
     * @param Invoice         $invoice
     * @param EmailValue|null $recipient
     * @param string|null     $recipientName
     *
     * @return void
     */
    public function send(Order $order, Invoice $invoice, EmailValue $recipient = null, $recipientName = null)
    {
        $deCountries = array(
            'DE',
            'AT',
            'CH'
        );
        $locale      = 'en';
        $dateFormat  = 'M. jS Y';
        if (in_array($order->getCountry()->toScalar(), $deCountries)) {
            $locale     = 'de';
            $dateFormat = 'd.m.Y';
        }

        $symbol          = $invoice->getCurrency()->equals(new IdentValue(Invoice::CURRENCY_USD)) ? '$' : '€';
        $rules           = $this->vatRules->getRules(
            $invoice->getOrganization()->isDefined(),
            $invoice->getCountry(),
            $invoice->getVatNo()->isDefined()
        );
        $countryName     = $invoice->getCountry()->toScalar();
        $countryOptional = $this->countryRepo->getCountryByIso($invoice->getCountry());
        if ($countryOptional->isDefined()) {
            /** @var CountryModel $country */
            $country     = $countryOptional->get();
            $countryName = $country->name;
        }
        $data = array(
            'firstname' => $order->getFirstname(),
            'lastname'  => $order->getLastname(),
            'email'     => $order->getEmail()->toScalar(),
            'domain'    => $order->getDomain()->toUTF8(),
            'invoice'   => array(
                'no'                       => $invoice->getNo(),
                'created'                  => $invoice->getCreated()->format($dateFormat),
                'fullname'                 => $invoice->getFullname(),
                'address1'                 => $invoice->getAddress1(),
                'address2'                 => $invoice->getAddress2(),
                'country'                  => $countryName,
                'organization'             => $invoice->getOrganization()->getOrElse(null),
                'vatNo'                    => $invoice->getVatNo()->getOrElse(null),
                'item_description'         => $invoice->getItemDescription(),
                'item_price'               => $this->moneyFormat->format($invoice->getItemPrice() / 100, $locale, $symbol),
                'vat_percent'              => $invoice->getVatPercent(),
                'vat_price'                => $this->moneyFormat->format($invoice->getVatPrice() / 100, $locale, $symbol),
                'total_price'              => $this->moneyFormat->format($invoice->getTotalPrice() / 100, $locale, $symbol),
                'show_reverse_charge_note' => $rules->showReverseChargeNote()
            )
        );

        $recipientName = Option::fromValue($recipientName)->getOrElse($order->getFirstname() . ' ' . $order->getLastname());
        $recipient     = Option::fromValue($recipient)->getOrElse($order->getEmail());

        $this->contentMailer->sendContentTemplateMail('shop.order', $locale, (string)$recipient, $recipientName, $data);
    }
}
