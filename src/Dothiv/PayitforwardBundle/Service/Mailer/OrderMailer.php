<?php

namespace Dothiv\PayitforwardBundle\Service\Mailer;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BaseWebsiteBundle\Service\MoneyFormatServiceInterface;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Service\VatRulesInterface;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Entity\Voucher;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
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
     * @param Order                   $order
     * @param Invoice                 $invoice
     * @param ArrayCollection|Voucher $vouchers
     * @param EmailValue|null         $recipient
     * @param string|null             $recipientName
     *
     * @return void
     */
    public function send(Order $order, Invoice $invoice, ArrayCollection $vouchers, EmailValue $recipient = null, $recipientName = null)
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

        $rules  = $this->vatRules->getRules(
            $invoice->getOrganization()->isDefined(),
            $invoice->getCountry(),
            $invoice->getVatNo()->isDefined()
        );
        $domain = $order->getDomain();
        $data   = array(
            'firstname' => $order->getFirstname(),
            'surname'   => $order->getSurname(),
            'domain'    => !$domain ? null : $domain->toUTF8(),
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
            ),
            'vouchers'  => array()
        );

        $voucherIterator = $vouchers->getIterator();
        for ($i = 1; $i <= 3; $i++) {
            /** @var HivDomainValue $domain */
            $domainGetter = 'getDomain' . $i;
            $nameGetter   = 'getDomain' . $i . 'Name';
            $domain       = $order->$domainGetter();
            $name         = $order->$nameGetter();
            if (!$domain) {
                continue;
            }
            /** @var Voucher $voucher */
            $voucher            = $voucherIterator->current();
            $data['vouchers'][] = array(
                'code'   => $voucher->getCode()->toScalar(),
                'domain' => $domain->toUTF8(),
                'name'   => $name,
            );
            $voucherIterator->next();
        }

        $recipientName = Option::fromValue($recipientName)->getOrElse($order->getFirstname() . ' ' . $order->getSurname());
        $recipient     = Option::fromValue($recipient)->getOrElse($order->getEmail());

        $this->contentMailer->sendContentTemplateMail('payitforward.order', $locale, (string)$recipient, $recipientName, $data);
    }
}
