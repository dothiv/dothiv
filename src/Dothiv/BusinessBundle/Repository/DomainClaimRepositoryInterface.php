<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\DomainClaim;

interface DomainClaimRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param DomainClaim $domainClaim
     *
     * @return self
     */
    public function persist(DomainClaim $domainClaim);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
