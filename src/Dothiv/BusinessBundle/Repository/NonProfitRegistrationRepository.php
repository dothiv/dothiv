<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use PhpOption\Option;
use Symfony\Component\Validator\ValidatorInterface;

class NonProfitRegistrationRepository extends DoctrineEntityRepository implements NonProfitRegistrationRepositoryInterface
{
    use ValidatorTrait;

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
     * Returns a list of active nonprofit registraions.
     *
     * @param mixed|null $offsetKey
     * @param mixed|null $offsetDir
     *
     * @return PaginatedResult
     */
    public function getActivePaginated($offsetKey = null, $offsetDir = null)
    {
        list(, $total, $minKey, $maxKey) = $this->createQueryBuilder('r')->select('COUNT(r), MAX(r.id), MIN(r.id)')->getQuery()->getScalarResult()[0];
        $paginatedResult = new PaginatedResult(10, $total);
        $qb              = $this->createQueryBuilder('r');
        $offsetDir       = Option::fromValue($offsetDir)->getOrElse('forward');
        if (Option::fromValue($offsetKey)->isDefined()) {
            if ($offsetDir == 'back') {
                $qb->orderBy('r.id', 'ASC');
                $qb->andWhere('r.id > :offsetKey')->setParameter('offsetKey', $offsetKey);
            } else { // forward
                $qb->orderBy('r.id', 'DESC');
                $qb->andWhere('r.id < :offsetKey')->setParameter('offsetKey', $offsetKey);
            }

        } else {
            $qb->orderBy('r.id', 'DESC');
        }
        $qb->setMaxResults($paginatedResult->getItemsPerPage());

        $items = $qb
            ->getQuery()
            ->getResult();
        if ($offsetDir == 'back') {
            $items = array_reverse($items);
        }
        $result = new ArrayCollection($items);
        if ($result->count() == 0) {
            return $paginatedResult;
        }
        $paginatedResult->setResult($result);
        if ($result->count() == $paginatedResult->getItemsPerPage()) {
            $paginatedResult->setNextPageKey(function (NonProfitRegistration $registration) use ($maxKey) {
                return $registration->getId() != $maxKey ? $registration->getId() : null;
            });
        }
        if ($offsetKey !== null) {
            $paginatedResult->setPrevPageKey(function (NonProfitRegistration $registration) use ($minKey) {
                return $registration->getId() != $minKey ? $registration->getId() : null;
            });
        }

        return $paginatedResult;
    }

}
