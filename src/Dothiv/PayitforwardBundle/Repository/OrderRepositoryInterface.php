<?php

namespace Dothiv\PayitforwardBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Exception\InvalidArgumentException;

/**
 * This repository contains the Orders.
 */
interface OrderRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param Order $order
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(Order $order);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
    
    /**
     * Returns orders which have not been processed.
     * 
     * @return ArrayCollection|Order[]
     */
    function findNew();
}
