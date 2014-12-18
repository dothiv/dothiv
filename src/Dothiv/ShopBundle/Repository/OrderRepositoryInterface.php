<?php

namespace Dothiv\ShopBundle\Repository;

use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\Option;

interface OrderRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param Order $Order
     *
     * @return self
     */
    public function persist(Order $Order);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param HivDomainValue $domain
     *
     * @return Option of Order
     */
    public function findByDomain(HivDomainValue $domain);

} 
