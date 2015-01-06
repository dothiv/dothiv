<?php

namespace Dothiv\ShopBundle\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ValueObject\EmailValue;

interface OrderMailerInterface
{
    /**
     * Send the email for an order.
     *
     * @param Order           $order
     * @param Invoice         $invoice
     * @param EmailValue|null $recipient
     * @param string|null     $recipientName
     *
     * @return void
     */
    function send(Order $order, Invoice $invoice, EmailValue $recipient = null, $recipientName = null);
}
