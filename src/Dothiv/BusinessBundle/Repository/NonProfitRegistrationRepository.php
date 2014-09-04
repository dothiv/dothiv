<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Repository\Traits\PaginatedQueryTrait;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use PhpOption\Option;
use Symfony\Component\Validator\ValidatorInterface;

class NonProfitRegistrationRepository extends DoctrineEntityRepository implements NonProfitRegistrationRepositoryInterface
{
    use ValidatorTrait;
    use PaginatedQueryTrait;

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
     * {@inheritdoc}
     */
    public function getPaginated($offsetKey = null, $offsetDir = null)
    {
        return $this->buildPaginatedResult($this->createQueryBuilder('i'), $offsetKey, $offsetDir);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return $this->getNonProfitRegistrationByDomainName($identifier);
    }
}
