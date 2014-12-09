<?php


namespace Dothiv\HivDomainStatusBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use Dothiv\HivDomainStatusBundle\Exception\InvalidArgumentException;
use PhpOption\Option;

interface HivDomainCheckRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param HivDomainCheck $hivDomainCheck
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(HivDomainCheck $hivDomainCheck);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param Domain $domain
     *
     * @return Option of HivDomainCheck
     */
    public function findLatestForDomain(Domain $domain);
}
