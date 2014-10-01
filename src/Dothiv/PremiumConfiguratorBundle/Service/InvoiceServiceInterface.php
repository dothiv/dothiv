<?php

namespace Dothiv\PremiumConfiguratorBundle\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;

interface InvoiceServiceInterface
{
    /**
     * @param Subscription $subscription
     * @param \DateTime    $intervalStart
     *
     * @return Invoice
     */
    function createInvoiceForSubscription(Subscription $subscription, \DateTime $intervalStart);
} 
