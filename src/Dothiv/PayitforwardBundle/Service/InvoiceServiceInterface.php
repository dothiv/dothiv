<?php


namespace Dothiv\PayitforwardBundle\Service;

use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\PayitforwardBundle\Entity\Order;

interface InvoiceServiceInterface
{
    /**
     * Creates an invoice for an order.
     * 
     * @param Order $order
     *
     * @return Invoice
     */
    function createInvoice(Order $order);
} 
