<?php

namespace Dothiv\PayitforwardBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\PayitforwardBundle\Entity\Order;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;

class OrderRepository extends DoctrineEntityRepository implements OrderRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(Order $order)
    {
        $this->getEntityManager()->persist($this->validate($order));
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
    function findNew()
    {
        return new ArrayCollection($this->createQueryBuilder('o')
            ->andWhere('o.charge IS NULL')
            ->getQuery()
            ->getResult());
    }

}
