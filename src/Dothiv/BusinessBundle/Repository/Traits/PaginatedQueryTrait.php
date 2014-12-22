<?php

namespace Dothiv\BusinessBundle\Repository\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Repository\CRUD;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\ValueObjectInterface;
use Symfony\Component\Validator\Constraints\DateTime;

trait PaginatedQueryTrait
{
    /**
     * Builds a paginated result.
     *
     * @param QueryBuilder               $qb
     * @param CRUD\PaginatedQueryOptions $options
     *
     * @return CRUD\PaginatedResult
     */
    protected function buildPaginatedResult(QueryBuilder $qb, CRUD\PaginatedQueryOptions $options)
    {
        $sortField = $options->getSortField()->getOrElse(new IdentValue('id'))->toScalar();
        if (strpos($sortField, '.') === false) {
            $sortField = 'i.' . $sortField;
        }
        $statsQb = clone $qb;
        list(, $total, $minKey, $maxKey)
            = $statsQb->select(sprintf('COUNT(i), MAX(%s), MIN(%s)', $sortField, $sortField))
            ->getQuery()->getScalarResult()[0];
        $paginatedResult = new CRUD\PaginatedResult(10, $total);
        $sortDir         = $options->getSortDir()->getOrElse('desc');
        if (strtolower($sortDir) == 'desc') {
            $qb->orderBy(sprintf('%s', $sortField), 'DESC');
            if ($options->getOffsetKey()->isDefined()) {
                $qb->andWhere(sprintf('%s < :offsetKey', $sortField))->setParameter('offsetKey', $options->getOffsetKey()->get());
            }
        } else { // forward
            $qb->orderBy(sprintf('%s', $sortField), 'ASC');
            if ($options->getOffsetKey()->isDefined()) {
                $qb->andWhere(sprintf('%s > :offsetKey', $sortField))->setParameter('offsetKey', $options->getOffsetKey()->get());
            }
        }
        $qb->setMaxResults($paginatedResult->getItemsPerPage());

        $items  = $qb
            ->getQuery()
            ->getResult();
        $result = new ArrayCollection($items);
        if ($result->count() == 0) {
            return $paginatedResult;
        }
        $paginatedResult->setResult($result);
        $offsetGetter = 'get' . ucfirst($options->getSortField()->getOrElse(new IdentValue('id'))->toScalar());
        $toScalar = function($offsetKey) {
            if ($offsetKey instanceof ValueObjectInterface) {
                return $offsetKey->toScalar();
            }
            if ($offsetKey instanceof \DateTime) {
                return $offsetKey->format('Y-m-d H:i:s');
            }
            return $offsetKey;
        };
        if ($result->count() == $paginatedResult->getItemsPerPage()) {
            $paginatedResult->setNextPageKey(function (EntityInterface $item) use ($maxKey, $offsetGetter, $toScalar) {
                $offsetValue = $toScalar($item->$offsetGetter());
                return $offsetValue != $maxKey ? $offsetValue : null;
            });
        }
        if ($options->getOffsetKey()->isDefined()) {
            $paginatedResult->setPrevPageKey(function (EntityInterface $item) use ($minKey, $offsetGetter, $toScalar) {
                $offsetValue = $toScalar($item->$offsetGetter());
                return $offsetValue != $minKey ? $offsetValue : null;
            });
        }

        return $paginatedResult;
    }
}
