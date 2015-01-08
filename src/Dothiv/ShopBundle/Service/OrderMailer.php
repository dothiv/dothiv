<?php

namespace Dothiv\ShopBundle\Service;

use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BaseWebsiteBundle\Service\MoneyFormatServiceInterface;
use Dothiv\BusinessBundle\Entity\Invoice;
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
     * @param ContentMailerInterface      $contentMailer
     * @param MoneyFormatServiceInterface $moneyFormatService
     */
    public function __construct(
        ContentMailerInterface $contentMailer,
        MoneyFormatServiceInterface $moneyFormatService
    )
    {
        $this->contentMailer = $contentMailer;
        $this->moneyFormat   = $moneyFormatService;
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
            'Deutschland',
            'Österreich',
            'Schweiz'
        );
        $locale      = 'en';
        $dateFormat  = 'M. jS Y';
        foreach ($deCountries as $c) {
            if (stristr($order->getCountry(), $c) !== false) {
                $locale     = 'de';
                $dateFormat = 'd.m.Y';
            }
        }

        $symbol = $invoice->getCurrency()->equals(new IdentValue(Invoice::CURRENCY_USD)) ? '$' : '€';

        $data = array(
            'firstname' => $order->getFirstname(),
            'lastname'  => $order->getLastname(),
            'email'     => $order->getEmail()->toScalar(),
            'domain'    => $order->getDomain()->toUTF8(),
            'invoice'   => array(
                'no'               => $invoice->getNo(),
                'created'          => $invoice->getCreated()->format($dateFormat),
                'fullname'         => $invoice->getFullname(),
                'address1'         => $invoice->getAddress1(),
                'address2'         => $invoice->getAddress2(),
                'country'          => $invoice->getCountry(),
                'vatNo'            => $invoice->getVatNo(),
                'item_description' => $invoice->getItemDescription(),
                'item_price'       => $this->moneyFormat->format($invoice->getItemPrice() / 100, $locale, $symbol),
                'vat_percent'      => $invoice->getVatPercent(),
                'vat_price'        => $this->moneyFormat->format($invoice->getVatPrice() / 100, $locale, $symbol),
                'total_price'      => $this->moneyFormat->format($invoice->getTotalPrice() / 100, $locale, $symbol)
            )
        );

        $recipientName = Option::fromValue($recipientName)->getOrElse($order->getFirstname() . ' ' . $order->getLastname());
        $recipient     = Option::fromValue($recipient)->getOrElse($order->getEmail());

        $this->contentMailer->sendContentTemplateMail('shop.order', $locale, (string)$recipient, $recipientName, $data);
    }
}
