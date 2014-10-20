<?php

namespace Dothiv\PremiumConfiguratorBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\PremiumConfiguratorBundle\Exception\InvalidArgumentException;
use PhpOption\Option;
use Symfony\Component\Validator\ValidatorInterface;

class PremiumBannerRepository extends DoctrineEntityRepository implements PremiumBannerRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function findByBanner(Banner $banner)
    {
        return Option::fromValue(
            $this->createQueryBuilder('p')
                ->andWhere('p.banner = :banner')->setParameter('banner', $banner)
                ->leftJoin('p.visual', 'v')
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function persist(PremiumBanner $premiumBanner)
    {
        $this->getEntityManager()->persist($this->validate($premiumBanner));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findUpdatedSince(\DateTime $dateTime)
    {
        return new ArrayCollection($this->createQueryBuilder('p')
            ->andWhere('p.updated >= :dateTime')->setParameter('dateTime', $dateTime)
            ->getQuery()
            ->getResult());
    }
}
