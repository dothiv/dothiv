<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Banner;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class BannerRepository extends DoctrineEntityRepository implements BannerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function persist(Banner $banner)
    {
        $this->getEntityManager()->persist($banner);
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
     * @param \DateTime $dateTime
     *
     * @return ArrayCollection|Banner[]
     */
    public function findUpdatedSince(\DateTime $dateTime)
    {
        return new ArrayCollection($this->createQueryBuilder('b')
            ->andWhere('b.updated >= :dateTime')->setParameter('dateTime', $dateTime)
            ->getQuery()
            ->getResult());
    }

}
