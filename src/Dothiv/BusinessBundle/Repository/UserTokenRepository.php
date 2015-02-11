<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Repository\Traits;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;

class UserTokenRepository extends DoctrineEntityRepository implements UserTokenRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;

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
        $this->getEntityManager()->persist($this->validate($userToken));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createItem()
    {
        return new UserToken();
    }

    /**
     * {@inheritdoc}
     */
    public function persistItem(EntityInterface $item)
    {
        return $this->persist($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return Option::fromValue($this->createQueryBuilder('ut')
            ->andWhere('ut.token = :identifier')->setParameter('identifier', $identifier)
            ->leftJoin('ut.user', 'u')
            ->getQuery()
            ->getOneOrNullResult());
    }

}
