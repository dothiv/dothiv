<?php

namespace Dothiv\CharityWebsiteBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use Dothiv\CharityWebsiteBundle\Entity\DomainNotification;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\ValueObject\IdentValue;

class DomainNotificationRepository extends DoctrineEntityRepository implements DomainNotificationRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(DomainNotification $domainNotification)
    {
        $this->getEntityManager()->persist($this->validate($domainNotification));
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
    public function findByDomain(Domain $domain, IdentValue $type)
    {
        return new ArrayCollection(
            $this->createQueryBuilder('n')
                ->andWhere('n.domain = :domain')->setParameter('domain', $domain)
                ->andWhere('n.type = :type')->setParameter('type', $type)
                ->getQuery()
                ->getResult()
        );
    }

}
