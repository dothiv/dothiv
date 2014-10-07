<?php

namespace Dothiv\PayitforwardBundle\Service\Mailer;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\PayitforwardBundle\Entity\Order;

interface OrderMailerInterface
{
    /**
     * Send the email for an order.
     *
     * @param Order           $order
     * @param Invoice         $invoice
     * @param ArrayCollection $vouchers
     *
     * @return void
     */
    function send(Order $order, Invoice $invoice, ArrayCollection $vouchers);
} 
