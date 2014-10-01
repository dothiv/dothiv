<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use PhpOption\Option;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class UserRepository extends DoctrineEntityRepository implements UserRepositoryInterface
{
    use ValidatorTrait;
    
    /**
     * @param string $email
     *
     * @return Option
     */
    public function getUserByEmail($email)
    {
        return Option::fromValue(
            $this->createQueryBuilder('u')
                ->andWhere('u.email = :email')->setParameter('email', strtolower($email))
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function persist(User $user)
    {
        $this->getEntityManager()->persist($this->validate($user));
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
}
