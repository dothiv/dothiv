<?php

namespace Dothiv\PayitforwardBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\PayitforwardBundle\Entity\Order;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use Dothiv\PayitforwardBundle\Exception\EntityNotFoundException;
use PhpOption\Option;

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

    /**
     * @param int $id
     *
     * @return Order
     *
     * @throws EntityNotFoundException if order is not found.
     */
    public function getById($id)
    {
        return Option::fromValue($this->find($id))->getOrCall(function() use($id) {
            throw new EntityNotFoundException();
        });
    }

}
