<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\Domain;
use PhpOption\Option;
use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

interface DomainRepositoryInterface
{
    /**
     * @param string $name
     *
     * @return Option
     */
    public function getDomainByName($name);

    /**
     * Persist the entity.
     *
     * @param Domain $domain
     *
     * @return self
     */
    public function persist(Domain $domain);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
