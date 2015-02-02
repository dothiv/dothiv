<?php

namespace Dothiv\PremiumConfiguratorBundle\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Repository\InvoiceRepositoryInterface;
use Dothiv\BusinessBundle\Service\VatRulesInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\ValueObject\IdentValue;
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
     * @var VatRulesInterface
     */
    private $vatRules;

    public function __construct(InvoiceRepositoryInterface $repo, $premiumPrice, VatRulesInterface $vatRules)
    {
        $this->repo         = $repo;
        $this->premiumPrice = $premiumPrice;
        $this->vatRules     = $vatRules;
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
        $invoice->setOrganization($subscription->getOrganization()->getOrElse(null));
        $invoice->setVatNo($subscription->getVatNo()->getOrElse(null));
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
        $rules = $this->vatRules->getRules(
            $subscription->getOrganization()->isDefined(),
            $subscription->getCountry(),
            $subscription->getVatNo()->isDefined()
        );
        $invoice->setVatPercent($rules->vatPercent());
        $invoice->setVatPrice((int)round($invoice->getItemPrice() * $invoice->getVatPercent() / 100, 0));
        $invoice->setTotalPrice($invoice->getVatPrice() + $invoice->getItemPrice());

        $this->repo->persist($invoice)->flush();

        return $invoice;
    }
}
