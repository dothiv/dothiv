<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\DomainClaim;
use PhpOption\Option;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class DomainClaimRepository extends DoctrineEntityRepository implements DomainClaimRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function persist(DomainClaim $domainClaim)
    {
        $this->getEntityManager()->persist($domainClaim);
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
