<?php

namespace Dothiv\PayitforwardBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Entity\Voucher;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;
use Dothiv\BusinessBundle\Repository\Traits\ValidatorTrait;
use Dothiv\PayitforwardBundle\Exception\InsufficientResourcesException;

class VoucherRepository extends DoctrineEntityRepository implements VoucherRepositoryInterface
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    public function persist(Voucher $voucher)
    {
        $this->getEntityManager()->persist($this->validate($voucher));
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
    public function findUnassigned($num)
    {
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.order IS NULL')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult();
        if (count($result) != $num) {
            throw new InsufficientResourcesException();
        }
        return new ArrayCollection($result);
    }

    /**
     * {@inheritdoc}
     */
    public function findAssigned(Order $order)
    {
        return new ArrayCollection($this->createQueryBuilder('o')
            ->andWhere('o.order = :order')->setParameter('order', $order)
            ->getQuery()
            ->getResult());
    }

}
