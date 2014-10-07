<?php

namespace Dothiv\PayitforwardBundle\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\PayitforwardBundle\Entity\Order;
use PhpOption\Option;

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
     * @var int
     */
    private $deVat;

    public function __construct(InvoiceRepositoryInterface $repo, $payitforwardPrice, $deVat)
    {
        $this->repo              = $repo;
        $this->payitforwardPrice = $payitforwardPrice;
        $this->deVat             = $deVat;
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
        $invoice->setVatNo(Option::fromValue($order->getTaxNo())->orElse(Option::fromValue($order->getVatNo()))->getOrElse(null));

        $numVouchers = $order->getNumVouchers();
        $invoice->setItemPrice($this->payitforwardPrice * $numVouchers);
        $invoice->setItemDescription(
            sprintf(
                '%d payitforward vouchers',
                $numVouchers
            )
        );
        switch ($order->getType()) {
            case Order::TYPE_NONEU:
                $invoice->setVatPercent(0);
                break;
            case Order::TYPE_EUORGNET:
                $invoice->setVatPercent(0);
                break;
            case Order::TYPE_EUORG:
                $invoice->setVatPercent($this->deVat);
                break;
            case Order::TYPE_DEORG:
                $invoice->setVatPercent($this->deVat);
                break;
            case Order::TYPE_EUPRIVATE:
                $invoice->setVatPercent($this->deVat);
                break;
        }
        $invoice->setVatPrice((int)round($invoice->getItemPrice() * $invoice->getVatPercent() / 100, 0));
        $invoice->setTotalPrice($invoice->getVatPrice() + $invoice->getItemPrice());

        $this->repo->persist($invoice)->flush();

        return $invoice;
    }
} 
