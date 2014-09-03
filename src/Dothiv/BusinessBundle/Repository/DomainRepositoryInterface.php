<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\Domain;
use PhpOption\Option;

interface DomainRepositoryInterface extends ObjectRepository, CRUDRepository
{
    /**
     * @param string $name
     *
     * @return Option
     */
    public function getDomainByName($name);

    /**
     * @param string $token
     *
     * @return Option
     */
    public function getDomainByToken($token);

    /**
     * Persist the entity.
     *
     * @param Domain $domain
     *
     * @return self
     */
    public function persist(Domain $domain);

    /**
     * Remove the entity.
     *
     * @param Domain $domain
     *
     * @return self
     */
    public function remove(Domain $domain);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
