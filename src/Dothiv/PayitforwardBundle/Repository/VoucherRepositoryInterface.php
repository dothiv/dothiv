<?php

namespace Dothiv\PayitforwardBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Entity\Voucher;
use Dothiv\PayitforwardBundle\Exception\InsufficientResourcesException;
use Dothiv\PayitforwardBundle\Exception\InvalidArgumentException;

/**
 * This repository contains the Vouchers.
 */
interface VoucherRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param Voucher $voucher
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(Voucher $voucher);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * Return $num unassigned vouches.
     *
     * @param int $num
     *
     * @return ArrayCollection|Voucher
     * @throws InsufficientResourcesException if number of unassigned vouchers is less than $num
     */
    public function findUnassigned($num);

    /**
     * Returns the vouchers assigned to Order $order
     *
     * @param Order $order
     *
     * @return ArrayCollection|Voucher
     */
    public function findAssigned(Order $order);
}
