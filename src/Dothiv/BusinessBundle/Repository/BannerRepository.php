<?php

namespace Dothiv\BusinessBundle\Repository;

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
}
