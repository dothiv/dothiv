<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
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
        if ($filterQuery->getSingleProperty('transfer')->isDefined()) {
            $qb->andWhere('i.transfer = :transfer')->setParameter('transfer', (int)$filterQuery->getSingleProperty('transfer')->get()->getValue());
        }
        if ($filterQuery->getSingleProperty('nonprofit')->isDefined()) {
            $qb->andWhere('i.nonprofit = :nonprofit')->setParameter('nonprofit', (int)$filterQuery->getSingleProperty('nonprofit')->get()->getValue());
        }
        $this->mapProperty('live', $filterQuery, $qb, true, '0');
        $this->mapProperty('clickcount', $filterQuery, $qb);
        $this->mapProperty('owner', $filterQuery, $qb, true, '0');
        if ($filterQuery->getSingleProperty('clickcounterconfig')->isDefined()) {
            if ((int)$filterQuery->getSingleProperty('clickcounterconfig')->get()->getValue()) {
                $qb->andWhere('i.activeBanner IS NOT NULL');
            } else {
                $qb->andWhere('i.activeBanner IS NULL');
            }
        }
        if ($filterQuery->getSingleProperty('registrar')->isDefined()) {
            // TODO: Implement URL to public-id for entities.
            $url       = new URLValue($filterQuery->getSingleProperty('registrar')->get()->getValue());
            $pathParts = explode('/', $url->getPath());
            $qb->leftJoin('i.registrar', 'r');
            $qb->andWhere('r.extId = :extId')->setParameter('extId', array_pop($pathParts));
        }
        if ($filterQuery->getProperty('created')->isDefined()) {
            $qb->leftJoin('DothivBusinessBundle:DomainWhois', 'w', Expr\Join::WITH, 'w.domain = i.name');
            $this->mapProperty('created', $filterQuery, $qb, null, null, 'w.creationDate');
        }
        return $this->buildPaginatedResult($qb, $options);
    }

    protected function mapProperty($name, FilterQuery $filterQuery, QueryBuilder $qb, $nullableColumn = null, $nullValue = null, $field = null)
    {
        $nullableColumn           = Option::fromValue($nullableColumn)->getOrElse(false);
        $nullValue                = Option::fromValue($nullValue)->getOrElse('0');
        $field                    = Option::fromValue($field)->getOrElse('i.' . $name);
        $applyFilterQueryProperty = function (FilterQueryProperty $property, $index) use ($qb, $nullableColumn, $nullValue, $field) {
            $value            = $property->getValue();
            $placeholderValue = str_replace('.', '_', $field) . $index;
            $placeholder      = ':' . $placeholderValue;
            if ($property->equals()) {
                if ($nullableColumn && $value === $nullValue) {
                    $qb->andWhere($qb->expr()->isNull($field));
                } else {
                    $qb->andWhere($qb->expr()->eq($field, $placeholder))->setParameter($placeholderValue, $value);
                }
            }
            if ($property->notEquals()) {
                if ($nullableColumn && $value === $nullValue) {
                    $qb->andWhere($qb->expr()->isNotNull($field));
                } else {
                    $qb->andWhere($qb->expr()->neq($field, $placeholder))->setParameter($placeholderValue, $value);
                }
            }
            if ($property->greaterThan()) {
                if ($nullableColumn) {
                    $qb->andWhere($qb->expr()->isNotNull($field));
                }
                $qb->andWhere($qb->expr()->gt($field, $placeholder))->setParameter($placeholderValue, $value);
            }
            if ($property->lessThan()) {
                if ($nullableColumn) {
                    $qb->andWhere($qb->expr()->isNotNull($field));
                }
                $qb->andWhere($qb->expr()->lt($field, $placeholder))->setParameter($placeholderValue, $value);
            }
            if ($property->greaterOrEqualThan()) {
                if ($nullableColumn) {
                    $qb->andWhere($qb->expr()->isNotNull($field));
                }
                $qb->andWhere($qb->expr()->gte($field, $placeholder))->setParameter($placeholderValue, $value);
            }
            if ($property->lessOrEqualThan()) {
                if ($nullableColumn) {
                    $qb->andWhere($qb->expr()->isNotNull($field));
                }
                $qb->andWhere($qb->expr()->lte($field, $placeholder))->setParameter($placeholderValue, $value);
            }
        };
        $filterQuery->getProperty($name)->map(function (array $filterProperties) use ($applyFilterQueryProperty) {
            foreach ($filterProperties as $i => $filterProperty) {
                $applyFilterQueryProperty($filterProperty, $i);
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
