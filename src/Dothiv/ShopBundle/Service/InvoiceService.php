<?php

namespace Dothiv\ShopBundle\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\BusinessBundle\Service\VatRulesInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ValueObject\IdentValue;

class InvoiceService implements InvoiceServiceInterface
{

    /**
     * @var InvoiceRepositoryInterface
     */
    private $repo;

    /**
     * @var DomainPriceServiceInterface
     */
    private $priceService;

    /**
     * @var VatRulesInterface
     */
    private $vatRules;

    public function __construct(InvoiceRepositoryInterface $repo, DomainPriceServiceInterface $priceService, VatRulesInterface $vatRules)
    {
        $this->repo         = $repo;
        $this->priceService = $priceService;
        $this->vatRules     = $vatRules;
    }

    /**
     * @param Order $order
     *
     * @return Invoice
     */
    public function createInvoice(Order $order)
    {
        $price   = $this->priceService->getPrice($order->getDomain());
        $invoice = new Invoice();
        $invoice->setFullname($order->getFirstname() . ' ' . $order->getLastname());
        $invoice->setAddress1($order->getLocality());
        $invoice->setAddress2($order->getLocality2()->getOrElse(null));
        $invoice->setCountry($order->getCountry());
        $invoice->setVatNo($order->getVatNo()->getOrElse(null));
        $invoice->setItemPrice(
            $order->getDuration() *
            ($order->getCurrency()->equals(new IdentValue(Order::CURRENCY_EUR)) ? $price->getNetPriceEUR() : $price->getNetPriceUSD())
        );
        $invoice->setCurrency($order->getCurrency());
        $invoice->setItemDescription(
            sprintf(
                '%d year(s) domain registration fees for %s',
                $order->getDuration(),
                $order->getDomain()->toUTF8()
            )
        );

        // VAT
        $varRules = $this->vatRules->getRules($order->getOrganization()->isDefined(), $order->getCountry(), $order->getVatNo()->isDefined());
        $invoice->setVatPercent($varRules->vatPercent());
        $invoice->setVatPrice((int)round($invoice->getItemPrice() * $invoice->getVatPercent() / 100, 0));
        $invoice->setTotalPrice($invoice->getVatPrice() + $invoice->getItemPrice());
        $this->repo->persist($invoice)->flush();
        return $invoice;
    }
}
