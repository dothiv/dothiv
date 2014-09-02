<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\Domain;
use PhpOption\Option;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class DomainRepository extends DoctrineEntityRepository implements DomainRepositoryInterface
{
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
        $this->getEntityManager()->persist($domain);
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

}
