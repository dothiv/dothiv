<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\DeletedDomain;
use Dothiv\BusinessBundle\Repository\CRUD;

interface DeletedDomainRepositoryInterface extends ObjectRepository, CRUD\PaginatedReadEntityRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param DeletedDomain $DeletedDomain
     *
     * @return self
     */
    public function persist(DeletedDomain $DeletedDomain);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
