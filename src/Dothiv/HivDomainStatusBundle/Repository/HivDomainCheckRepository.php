<?php

namespace Dothiv\HivDomainStatusBundle\Repository;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Repository\Traits;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use PhpOption\Option;

class HivDomainCheckRepository extends DoctrineEntityRepository implements HivDomainCheckRepositoryInterface
{
    use Traits\ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(HivDomainCheck $HivDomainCheck)
    {
        $this->getEntityManager()->persist($this->validate($HivDomainCheck));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persistItem(EntityInterface $item)
    {
        $this->persist($item);
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
    public function findLatestForDomain(Domain $domain)
    {
        return Option::fromValue($this->createQueryBuilder('c')
            ->andWhere('c.domain = :domain')->setParameter('domain', $domain)
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult());
    }

}
