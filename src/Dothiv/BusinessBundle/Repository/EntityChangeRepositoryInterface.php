<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD;
use Dothiv\ValueObject\IdentValue;

interface EntityChangeRepositoryInterface
{
    /**
     * @param EntityChange $change
     *
     * @return self
     */
    public function persist(EntityChange $change);

    /**
     * @return self
     */
    public function flush();

    /**
     * Creates a paginated result of changes for the entity of type $entity with identifier $identifier
     *
     * @param                            $entity
     * @param IdentValue                 $identifier
     * @param CRUD\PaginatedQueryOptions $options
     * @param FilterQuery                $filterQuery
     *
     * @return CRUD\PaginatedResult
     */
    public function getPaginated($entity, IdentValue $identifier, CRUD\PaginatedQueryOptions $options, FilterQuery $filterQuery);
}
