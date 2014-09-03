<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\PremiumBid;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use PhpOption\Option;

/**
 * This repository contains the PremiumBids.
 */
interface PremiumBidRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param PremiumBid $nonProfitRegistration
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(PremiumBid $nonProfitRegistration);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * Returns a list of PremiumBids which notifications need to be sent.
     *
     * @return PremiumBid[]|ArrayCollection
     */
    public function getUnnotified();
}
