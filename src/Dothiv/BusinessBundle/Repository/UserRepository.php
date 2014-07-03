<?php

namespace Dothiv\BusinessBundle\Repository;

use PhpOption\Option;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class UserRepository extends DoctrineEntityRepository implements UserRepositoryInterface
{
    /**
     * @param string $email
     *
     * @return Option
     */
    public function getUserByEmail($email)
    {
        return Option::fromValue(
            $this->createQueryBuilder('u')
                ->andWhere('u.email = :email')->setParameter('email', $email)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

    /**
     * @param string $token
     *
     * @return Option
     */
    public function getUserByBearerToken($token)
    {
        return Option::fromValue(
            $this->createQueryBuilder('u')
                ->andWhere('u.bearerToken = :token')->setParameter('token', $token)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

} 
