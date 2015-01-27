<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\Traits;
use PhpOption\Option;

class NonProfitRegistrationRepository extends DoctrineEntityRepository implements NonProfitRegistrationRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\PaginatedQueryTrait;
    use Traits\GetItemEntityName;

    /**
     * {@inheritdoc}
     */
    public function persist(NonProfitRegistration $nonProfitRegistration)
    {
        $this->getEntityManager()->persist($this->validate($nonProfitRegistration));
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
     * @param string $domain
     *
     * @return Option
     */
    public function getNonProfitRegistrationByDomainName($domain)
    {
        return Option::fromValue($this->createQueryBuilder('r')
            ->andWhere('r.domain = :domain')->setParameter('domain', $domain)
            ->getQuery()
            ->getOneOrNullResult());
    }

    /**
     * Returns a list of unconfirmed NonProfitRegistrations.
     *
     * @return NonProfitRegistration[]|ArrayCollection
     */
    public function getUnconfirmed()
    {
        return new ArrayCollection($this->createQueryBuilder('r')
            ->andWhere('r.receiptSent IS NULL')
            ->getQuery()
            ->getResult());
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(CRUD\PaginatedQueryOptions $options, FilterQuery $filterQuery)
    {
        $qb = $this->createQueryBuilder('i');
        if ($filterQuery->getTerm()->isDefined()) {
            $qb->andWhere('i.domain LIKE :q')->setParameter('q', '%' . $filterQuery->getTerm()->get() . '%');
        }
        if ($filterQuery->getSingleProperty('approved')->isDefined()) {
            if ((int)$filterQuery->getSingleProperty('approved')->get()->getValue()) {
                $qb->andWhere('i.approved IS NOT NULL');
            } else {
                $qb->andWhere('i.approved IS NULL');
            }
        }
        if ($filterQuery->getSingleProperty('registered')->isDefined()) {
            if ((int)$filterQuery->getSingleProperty('registered')->get()->getValue()) {
                $qb->andWhere('i.registered IS NOT NULL');
            } else {
                $qb->andWhere('i.registered IS NULL');
            }
        }
        if ($filterQuery->getSingleProperty('older')->isDefined()) {
            $qb->andWhere('i.created < :older')->setParameter('older', $filterQuery->getSingleProperty('older')->get()->getValue());
        }
        return $this->buildPaginatedResult($qb, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return $this->getNonProfitRegistrationByDomainName($identifier);
    }
}
