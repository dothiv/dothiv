<?php

namespace Dothiv\ShopBundle\Service;


use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\ShopBundle\Entity\Order;

interface InvoiceServiceInterface
{
    /**
     * @param Order $order
     *
     * @return Invoice
     */
    public function createInvoice(Order $order);
} 
