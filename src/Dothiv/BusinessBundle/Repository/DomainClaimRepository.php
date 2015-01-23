<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\DomainClaim;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class DomainClaimRepository extends DoctrineEntityRepository implements DomainClaimRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(DomainClaim $domainClaim)
    {
        $this->getEntityManager()->persist($this->validate($domainClaim));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
        return $this;
    }
}
