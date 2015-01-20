<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Model\FilterQueryProperty;
use Dothiv\BusinessBundle\Repository\Traits;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;

class DomainRepository extends DoctrineEntityRepository implements DomainRepositoryInterface
{
    use Traits\PaginatedQueryTrait;
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;

    /**
     * @param string $name
     *
     * @return Option
     */
    public function getDomainByName($name)
    {
        return Option::fromValue(
            $this->createQueryBuilder('d')
                ->andWhere('d.name = :name')->setParameter('name', $name)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * @param string $token
     *
     * @return Option
     */
    public function getDomainByToken($token)
    {
        return Option::fromValue(
            $this->createQueryBuilder('d')
                ->andWhere('d.token = :token')->setParameter('token', $token)
                ->andWhere('d.token IS NOT NULL')
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findByOwnerEmail(EmailValue $email)
    {
        return new ArrayCollection(
            $this->createQueryBuilder('d')
                ->andWhere('d.ownerEmail = :email')->setParameter('email', $email->toScalar())
                ->getQuery()
                ->getResult()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Domain $domain)
    {
        $this->getEntityManager()->persist($this->validate($domain));
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
    public function remove(Domain $domain)
    {
        $this->getEntityManager()->remove($domain);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(CRUD\PaginatedQueryOptions $options, FilterQuery $filterQuery)
    {
        $qb = $this->createQueryBuilder('i');
        if ($filterQuery->getTerm()->isDefined()) {
            $qb->andWhere('i.name LIKE :q')->setParameter('q', '%' . $filterQuery->getTerm()->get() . '%');
        }
        if ($filterQuery->getProperty('transfer')->isDefined()) {
            $qb->andWhere('i.transfer = :transfer')->setParameter('transfer', (int)$filterQuery->getProperty('transfer')->get()->getValue());
        }
        if ($filterQuery->getProperty('nonprofit')->isDefined()) {
            $qb->andWhere('i.nonprofit = :nonprofit')->setParameter('nonprofit', (int)$filterQuery->getProperty('nonprofit')->get()->getValue());
        }
        if ($filterQuery->getProperty('live')->isDefined()) {
            $qb->andWhere('i.live = :live')->setParameter('live', (int)$filterQuery->getProperty('live')->get()->getValue());
        }
        $this->mapProperty('clickcount', $filterQuery, $qb);
        if ($filterQuery->getProperty('clickcounterconfig')->isDefined()) {
            if ((int)$filterQuery->getProperty('clickcounterconfig')->get()->getValue()) {
                $qb->andWhere('i.activeBanner IS NOT NULL');
            } else {
                $qb->andWhere('i.activeBanner IS NULL');
            }
        }
        if ($filterQuery->getProperty('registrar')->isDefined()) {
            // TODO: Implement URL to public-id for entities.
            $url       = new URLValue($filterQuery->getProperty('registrar')->get()->getValue());
            $pathParts = explode('/', $url->getPath());
            $qb->leftJoin('i.registrar', 'r');
            $qb->andWhere('r.extId = :extId')->setParameter('extId', array_pop($pathParts));
        }
        return $this->buildPaginatedResult($qb, $options);
    }

    protected function mapProperty($name, FilterQuery $filterQuery, QueryBuilder $qb)
    {
        $filterQuery->getProperty($name)->map(function (FilterQueryProperty $property) use ($qb, $name) {
            if ($property->equals()) {
                $qb->andWhere($qb->expr()->eq('i.' . $name, $property->getValue()));
            }
            if ($property->notEquals()) {
                $qb->andWhere($qb->expr()->neq('i.' . $name, $property->getValue()));
            }
            if ($property->greaterThan()) {
                $qb->andWhere($qb->expr()->gt('i.' . $name, $property->getValue()));
            }
            if ($property->lessThan()) {
                $qb->andWhere($qb->expr()->lt('i.' . $name, $property->getValue()));
            }
            if ($property->greaterOrEqualThan()) {
                $qb->andWhere($qb->expr()->gte('i.' . $name, $property->getValue()));
            }
            if ($property->lessOrEqualThan()) {
                $qb->andWhere($qb->expr()->lte('i.' . $name, $property->getValue()));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return $this->getDomainByName($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function findUninstalled()
    {
        return new ArrayCollection(
            $this->createQueryBuilder('d')
                ->andWhere('d.activeBanner IS NOT NULL')
                ->andWhere('d.clickcount = 0')
                ->getQuery()
                ->getResult()
        );
    }
}
