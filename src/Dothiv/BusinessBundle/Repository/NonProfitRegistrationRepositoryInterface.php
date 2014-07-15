<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use PhpOption\Option;

/**
 * This repository contains the NonProfitRegistrations.
 */
interface NonProfitRegistrationRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param NonProfitRegistration $nonProfitRegistration
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(NonProfitRegistration $nonProfitRegistration);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param string $domain
     *
     * @return Option
     */
    public function getNonProfitRegistrationByDomainName($domain);
}
