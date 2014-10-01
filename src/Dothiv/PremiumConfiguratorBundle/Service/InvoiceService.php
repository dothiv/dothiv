<?php

namespace Dothiv\PremiumConfiguratorBundle\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
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
    private $premiumPrice;

    /**
     * @var int
     */
    private $deVat;

    public function __construct(InvoiceRepositoryInterface $repo, $premiumPrice, $deVat)
    {
        $this->repo         = $repo;
        $this->premiumPrice = $premiumPrice;
        $this->deVat        = $deVat;
    }

    /**
     * @param Subscription $subscription
     * @param \DateTime    $intervalStart
     *
     * @return Invoice
     */
    public function createInvoiceForSubscription(Subscription $subscription, \DateTime $intervalStart)
    {
        $invoice = new Invoice();
        $invoice->setFullname($subscription->getFullname());
        $invoice->setAddress1($subscription->getAddress1());
        $invoice->setAddress2($subscription->getAddress2());
        $invoice->setCountry($subscription->getCountry());
        $invoice->setVatNo(Option::fromValue($subscription->getTaxNo())->orElse(Option::fromValue($subscription->getVatNo()))->getOrElse(null));
        $invoice->setItemPrice($this->premiumPrice);
        $intervalEnd = clone $intervalStart;
        $intervalEnd->modify('+1 month');
        $intervalEnd->modify('-1 day');
        $invoice->setItemDescription(
            sprintf(
                'Click-Counter premium subscription (monthly Price Plan) from %s to %s.',
                $intervalStart->format('M. jS Y'),
                $intervalEnd->format('M. jS Y')
            )
        );
        switch ($subscription->getType()) {
            case Subscription::TYPE_NONEU:
                $invoice->setVatPercent(0);
                break;
            case Subscription::TYPE_EUORGNET:
                $invoice->setVatPercent(0);
                break;
            case Subscription::TYPE_EUORG:
                $invoice->setVatPercent($this->deVat);
                break;
            case Subscription::TYPE_DEORG:
                $invoice->setVatPercent($this->deVat);
                break;
            case Subscription::TYPE_EUPRIVATE:
                $invoice->setVatPercent($this->deVat);
                break;
        }
        $invoice->setVatPrice((int)round($invoice->getItemPrice() * $invoice->getVatPercent() / 100, 0));
        $invoice->setTotalPrice($invoice->getVatPrice() + $invoice->getItemPrice());
        
        $this->repo->persist($invoice)->flush();
        
        return $invoice;
    }
} 
