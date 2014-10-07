<?php


namespace Dothiv\PayitforwardBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Entity\Voucher;
use Dothiv\PayitforwardBundle\Exception\InsufficientResourcesException;

interface OrderServiceInterface
{
    /**
     * Tries to assign vouchers to the order.
     *
     * @param Order $order
     *
     * @return ArrayCollection|Voucher
     * @throws InsufficientResourcesException if not enough voucher codes are available.
     */
    function assignVouchers(Order $order);
} 
