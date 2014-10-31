<?php

namespace Dothiv\BusinessBundle\Repository\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Repository\PaginatedResult;
use PhpOption\Option;

trait PaginatedQueryTrait
{
    /**
     * Builds a paginated result.
     *
     * @param QueryBuilder $qb
     * @param mixed|null   $offsetKey
     * @param mixed|null   $offsetDir
     * @param string       $sortField
     *
     * @return PaginatedResult
     */
    protected function buildPaginatedResult(QueryBuilder $qb, $offsetKey = null, $offsetDir = null, $sortField = null)
    {
        $sortField = Option::fromValue($sortField)->getOrElse('id');
        $statsQb   = clone $qb;
        list(, $total, $minKey, $maxKey)
            = $statsQb->select(sprintf('COUNT(i), MAX(i.%s), MIN(i.%s)', $sortField, $sortField))
            ->getQuery()->getScalarResult()[0];
        $paginatedResult = new PaginatedResult(10, $total);
        $offsetDir       = Option::fromValue($offsetDir)->getOrElse('forward');
        if (Option::fromValue($offsetKey)->isDefined()) {
            if ($offsetDir == 'back') {
                $qb->orderBy(sprintf('i.%s', $sortField), 'ASC');
                $qb->andWhere(sprintf('i.%s > :offsetKey', $sortField))->setParameter('offsetKey', $offsetKey);
            } else { // forward
                $qb->orderBy(sprintf('i.%s', $sortField), 'DESC');
                $qb->andWhere(sprintf('i.%s < :offsetKey', $sortField))->setParameter('offsetKey', $offsetKey);
            }
        } else {
            $qb->orderBy(sprintf('i.%s', $sortField), 'DESC');
        }
        $qb->setMaxResults($paginatedResult->getItemsPerPage());

        $items = $qb
            ->getQuery()
            ->getResult();
        $result = new ArrayCollection($items);
        if ($result->count() == 0) {
            return $paginatedResult;
        }
        $paginatedResult->setResult($result);
        $offsetGetter = 'get' . ucfirst($sortField);
        if ($result->count() == $paginatedResult->getItemsPerPage()) {
            $paginatedResult->setNextPageKey(function (EntityInterface $item) use ($maxKey, $offsetGetter) {
                return $item->$offsetGetter() != $maxKey ? $item->$offsetGetter() : null;
            });
        }
        if ($offsetKey !== null) {
            $paginatedResult->setPrevPageKey(function (EntityInterface $item) use ($minKey, $offsetGetter) {
                return $item->$offsetGetter() != $minKey ? $item->$offsetGetter() : null;
            });
        }

        return $paginatedResult;
    }
} 
