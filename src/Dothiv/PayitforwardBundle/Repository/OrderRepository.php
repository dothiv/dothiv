<?php

namespace Dothiv\PayitforwardBundle\Repository;

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
}
