<?php

namespace Dothiv\PremiumConfiguratorBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Exception\EntityNotFoundException;
use Dothiv\PremiumConfiguratorBundle\Exception\InvalidArgumentException;
use PhpOption\Option;

interface SubscriptionRepositoryInterface
{
    /**
     * @param Domain $domain
     *
     * @return Option
     */
    public function findByDomain(Domain $domain);

    /**
     * Returns the un-activated subscriptions.
     *
     * @return Subscription[]|ArrayCollection
     */
    public function findInactive();

    /**
     * Persist the entity.
     *
     * @param Subscription $subscription
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(Subscription $subscription);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param int $id
     *
     * @return Subscription
     *
     * @throws EntityNotFoundException if order is not found.
     */
    public function getById($id);
} 
