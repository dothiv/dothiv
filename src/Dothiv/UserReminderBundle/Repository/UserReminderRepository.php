<?php

namespace Dothiv\UserReminderBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\ValueObject\IdentValue;

class UserReminderRepository extends DoctrineEntityRepository implements UserReminderRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(UserReminder $UserReminder)
    {
        $this->getEntityManager()->persist($this->validate($UserReminder));
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
    public function findByTypeAndItem(IdentValue $type, EntityInterface $item)
    {
        return new ArrayCollection(
            $this->createQueryBuilder('n')
                ->andWhere('n.ident = :ident')->setParameter('ident', $item->getPublicId())
                ->andWhere('n.type = :type')->setParameter('type', $type)
                ->getQuery()
                ->getResult()
        );
    }

}
