<?php

namespace Dothiv\PayitforwardBundle\Service\Mailer;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\ValueObject\EmailValue;

interface OrderMailerInterface
{
    /**
     * Send the email for an order.
     *
     * @param Order           $order
     * @param Invoice         $invoice
     * @param ArrayCollection $vouchers
     * @param EmailValue|null $recipient
     * @param string|null     $recipientName
     *
     * @return void
     */
    function send(Order $order, Invoice $invoice, ArrayCollection $vouchers, EmailValue $recipient = null, $recipientName = null);
} 
