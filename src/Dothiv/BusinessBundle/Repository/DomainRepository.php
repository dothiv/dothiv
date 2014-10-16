<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\Traits\PaginatedQueryTrait;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use PhpOption\Option;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class DomainRepository extends DoctrineEntityRepository implements DomainRepositoryInterface
{
    use PaginatedQueryTrait;
    use ValidatorTrait;

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
    public function persist(Domain $domain)
    {
        $this->getEntityManager()->persist($this->validate($domain));
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
    public function getPaginated($offsetKey = null, $offsetDir = null)
    {
        return $this->buildPaginatedResult($this->createQueryBuilder('i'), $offsetKey, $offsetDir);
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
