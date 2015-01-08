<?php

namespace Dothiv\ShopBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Model\CountryModel;
use PhpOption\Option;

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
     * @var int
     */
    private $deVat;

    /**
     * @var ArrayCollection|CountryModel
     */
    private $countries;

    public function __construct(InvoiceRepositoryInterface $repo, DomainPriceServiceInterface $priceService, $deVat)
    {
        $this->repo         = $repo;
        $this->priceService = $priceService;
        $this->deVat        = $deVat;
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
            ($order->getCurrency() == Order::CURRENCY_EUR ? $price->getNetPriceEUR() : $price->getNetPriceUSD())
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
        $invoice->setVatPercent(0);
        if ($order->getOrganization()->isDefined()) {
            $countries = $this->getCountries();
            $country   = $countries->filter(function (CountryModel $c) use ($order) {
                return $c->name == $order->getCountry();
            })->first();
            if (Option::fromValue($country)->isDefined()) {
                $invoice->setVatPercent($this->deVat);
            }
        }
        $invoice->setVatPrice((int)round($invoice->getItemPrice() * $invoice->getVatPercent() / 100, 0));
        $invoice->setTotalPrice($invoice->getVatPrice() + $invoice->getItemPrice());

        $this->repo->persist($invoice)->flush();

        return $invoice;
    }

    protected function getCountries()
    {
        if ($this->countries == null) {
            $this->countries = new ArrayCollection();
            foreach (json_decode(file_get_contents(__DIR__ . '/../../BaseWebsiteBundle/Resources/public/data/countries.json')) as $countryData) {
                $country       = new CountryModel();
                $country->name = $countryData[0];
                $country->eu   = $countryData[1];
                $this->countries->add($country);
            }
        }
        return $this->countries;
    }
}
