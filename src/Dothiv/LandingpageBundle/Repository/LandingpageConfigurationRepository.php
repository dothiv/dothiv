<?php

namespace Dothiv\LandingpageBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\Traits;
use Dothiv\LandingpageBundle\Entity\Landingpageconfiguration;
use PhpOption\Option;

class LandingpageConfigurationRepository extends EntityRepository implements LandingpageConfigurationRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;

    /**
     * {@inheritdoc}
     */
    public function persist(Landingpageconfiguration $landingpageconfiguration)
    {
        $this->getEntityManager()->persist($this->validate($landingpageconfiguration));
        return $this;
    }

    /**
     * @return self
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function findByDomain(Domain $domain)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('c.domain = :domain')->setParameter('domain', $domain);
        return Option::fromValue($qb->getQuery()->getOneOrNullResult());
    }
}
