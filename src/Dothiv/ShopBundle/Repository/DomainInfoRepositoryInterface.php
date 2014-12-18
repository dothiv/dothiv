<?php

namespace Dothiv\ShopBundle\Repository;

use Dothiv\ShopBundle\Entity\DomainInfo;
use Dothiv\ValueObject\HivDomainValue;

interface DomainInfoRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param DomainInfo $domainInfo
     *
     * @return self
     */
    public function persist(DomainInfo $domainInfo);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param HivDomainValue $name
     *
     * @return DomainInfo
     */
    public function getByDomain(HivDomainValue $name);

} 
