<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\PremiumBid;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;

class PremiumBidRepository extends DoctrineEntityRepository implements PremiumBidRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(PremiumBid $premiumBid)
    {
        $this->getEntityManager()->persist($this->validate($premiumBid));
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

    /**
     * {@inheritdoc}
     */
    public function getUnnotified()
    {
        return new ArrayCollection($this->createQueryBuilder('r')
            ->andWhere('r.notified IS NULL')
            ->getQuery()
            ->getResult());
    }
}
