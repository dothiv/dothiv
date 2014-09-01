<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\ValueObject\IdentValue;
use PhpOption\Option;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class UserTokenRepository extends DoctrineEntityRepository implements UserTokenRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getActiveTokens(User $user, IdentValue $scope, \DateTime $minLifetime)
    {
        return new ArrayCollection($this->createQueryBuilder('ut')
            ->andWhere('ut.user = :user')->setParameter('user', $user)
            ->andWhere('ut.scope = :scope')->setParameter('scope', $scope)
            ->andWhere('ut.lifeTime > :minLifeTime')->setParameter('minLifeTime', $minLifetime)
            ->getQuery()
            ->getResult());
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenByBearerToken($bearerToken)
    {
        return Option::fromValue($this->createQueryBuilder('ut')
            ->andWhere('ut.bearerToken = :bearerToken')->setParameter('bearerToken', $bearerToken)
            ->leftJoin('ut.user', 'u')
            ->getQuery()
            ->getOneOrNullResult());
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
    public function persist(UserToken $userToken)
    {
        $this->getEntityManager()->persist($userToken);
        return $this;
    }
}
