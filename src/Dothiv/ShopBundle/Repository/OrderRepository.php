<?php

namespace Dothiv\ShopBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\BusinessBundle\Repository\Traits;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\Option;

class OrderRepository extends EntityRepository implements OrderRepositoryInterface
{
    use Traits\ValidatorTrait;
    use Traits\GetItemEntityName;

    /**
     * @param Order $Order
     *
     * @return self
     */
    public function persist(Order $Order)
    {
        $this->getEntityManager()->persist($this->validate($Order));
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persistItem(EntityInterface $item)
    {
        $this->persist($item);
        return $this;
    }

    /**
     * @return self
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
        return $this;
    }

    /**
     * @param HivDomainValue $domain
     *
     * @return Option of Order
     */
    public function findByDomain(HivDomainValue $domain)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->andWhere('o.domain = :domain')->setParameter('domain', $domain->toScalar());
        return Option::fromValue($qb->getQuery()->getOneOrNullResult());
    }

    /**
     * Creates a new entity.
     *
     * @return EntityInterface
     */
    public function createItem()
    {
        return new Order();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByIdentifier($identifier)
    {
        return Option::fromValue(
            $this->createQueryBuilder('d')
                ->andWhere('d.id = :id')->setParameter('id', $identifier)
                ->getQuery()
                ->getOneOrNullResult()
        );
    }

}
