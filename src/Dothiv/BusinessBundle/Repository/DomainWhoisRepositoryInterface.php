<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\DomainWhois;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\Option;

interface DomainWhoisRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param DomainWhois $domainWhois
     *
     * @return self
     */
    public function persist(DomainWhois $domainWhois);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param HivDomainValue $domain
     *
     * @return Option of DomainWhois
     */
    public function findByDomain(HivDomainValue $domain);
}
