<?php

namespace Dothiv\PayitforwardBundle\Service\Mailer;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BaseWebsiteBundle\Service\MoneyFormatServiceInterface;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Entity\Voucher;
use Dothiv\ValueObject\HivDomainValue;

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
     * @param Order                   $order
     * @param Invoice                 $invoice
     * @param ArrayCollection|Voucher $vouchers
     *
     * @return void
     */
    public function send(Order $order, Invoice $invoice, ArrayCollection $vouchers)
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

        $domain = $order->getDomain();
        $data   = array(
            'firstname' => $order->getFirstname(),
            'surname'   => $order->getSurname(),
            'domain'    => empty($domain) ? null : $domain->toUTF8(),
            'invoice'   => array(
                'no'               => $invoice->getNo(),
                'created'          => $invoice->getCreated()->format($dateFormat),
                'fullname'         => $invoice->getFullname(),
                'address1'         => $invoice->getAddress1(),
                'address2'         => $invoice->getAddress2(),
                'country'          => $invoice->getCountry(),
                'vatNo'            => $invoice->getVatNo(),
                'item_description' => $invoice->getItemDescription(),
                'item_price'       => $this->moneyFormat->format($invoice->getItemPrice() / 100, $locale),
                'vat_percent'      => $invoice->getVatPercent(),
                'vat_price'        => $this->moneyFormat->format($invoice->getVatPrice() / 100, $locale),
                'total_price'      => $this->moneyFormat->format($invoice->getTotalPrice() / 100, $locale)
            ),
            'vouchers'  => array()
        );

        for ($i = 1; $i <= 3; $i++) {
            /** @var HivDomainValue $domain */
            $domainGetter = 'getDomain' . $i;
            $nameGetter   = 'getDomain' . $i . 'Name';
            $domain       = $order->$domainGetter();
            $name         = $order->$nameGetter();
            if (empty($domain)) {
                continue;
            }
            $data['vouchers'][] = array(
                'code'   => (string)$vouchers->current()->getCode(),
                'domain' => $domain->toUTF8(),
                'name'   => $name,
            );
            $vouchers->next();
        }

        $name = $order->getFirstname() . ' ' . $order->getSurname();

        $this->contentMailer->sendContentTemplateMail('payitforward.order', $locale, (string)$order->getEmail(), $name, $data);
    }
} 
