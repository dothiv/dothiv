<?php

namespace Dothiv\PayitforwardBundle\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\BusinessBundle\Service\VatRulesInterface;
use Dothiv\PayitforwardBundle\Entity\Order;

class InvoiceService implements InvoiceServiceInterface
{

    /**
     * @var InvoiceRepositoryInterface
     */
    private $repo;

    /**
     * @var int
     */
    private $payitforwardPrice;

    /**
     * @var VatRulesInterface
     */
    private $vatRules;

    /**
     * @param InvoiceRepositoryInterface $repo
     * @param int                        $payitforwardPrice
     * @param VatRulesInterface          $vatRules
     */
    public function __construct(InvoiceRepositoryInterface $repo, $payitforwardPrice, VatRulesInterface $vatRules)
    {
        $this->repo              = $repo;
        $this->payitforwardPrice = $payitforwardPrice;
        $this->vatRules          = $vatRules;
    }

    /**
     * {@inheritdoc}
     */
    public function createInvoice(Order $order)
    {
        $invoice = new Invoice();
        $invoice->setFullname($order->getFullname());
        $invoice->setAddress1($order->getAddress1());
        $invoice->setAddress2($order->getAddress2());
        $invoice->setCountry($order->getCountry());
        $invoice->setOrganization($order->getOrganization()->getOrElse(null));
        $invoice->setVatNo($order->getVatNo()->getOrElse(null));

        $numVouchers = $order->getNumVouchers();
        $invoice->setItemPrice($this->payitforwardPrice * $numVouchers);
        $invoice->setItemDescription(
            sprintf(
                '%d payitforward vouchers',
                $numVouchers
            )
        );
        $rules = $this->vatRules->getRules(
            $order->getOrganization()->isDefined(),
            $order->getCountry(),
            $order->getVatNo()->isDefined()
        );
        $invoice->setVatPercent($rules->vatPercent());
        $invoice->setVatPrice((int)round($invoice->getItemPrice() * $invoice->getVatPercent() / 100, 0));
        $invoice->setTotalPrice($invoice->getVatPrice() + $invoice->getItemPrice());

        $this->repo->persist($invoice)->flush();

        return $invoice;
    }
}
