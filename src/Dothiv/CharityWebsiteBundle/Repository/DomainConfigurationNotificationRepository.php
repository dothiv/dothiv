<?php

namespace Dothiv\CharityWebsiteBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use Dothiv\CharityWebsiteBundle\Entity\DomainConfigurationNotification;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class DomainConfigurationNotificationRepository extends DoctrineEntityRepository implements DomainConfigurationNotificationRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(DomainConfigurationNotification $domainConfigurationNotification)
    {
        $this->getEntityManager()->persist($this->validate($domainConfigurationNotification));
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
    public function findByDomain(Domain $domain)
    {
        return new ArrayCollection(
            $this->createQueryBuilder('n')
                ->andWhere('n.domain = :domain')->setParameter('domain', $domain)
                ->getQuery()
                ->getResult()
        );
    }

}
