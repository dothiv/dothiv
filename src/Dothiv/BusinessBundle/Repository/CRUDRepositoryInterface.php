<?php


namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\FilterQuery;
use PhpOption\Option;

interface CRUDRepositoryInterface
{
    /**
     * Returns a list of items
     *
     * @param PaginatedQueryOptions $options
     * @param FilterQuery           $filterQuery
     *
     * @return PaginatedResult
     */
    public function getPaginated(PaginatedQueryOptions $options, FilterQuery $filterQuery);

    /**
     * Returns a single item
     *
     * @param string $identifier
     *
     * @return Option of EntityInterface
     */
    public function getItemByIdentifier($identifier);

    /**
     * Returns the entity name (e.g. "AcmeBundle:Entity") for the $item.
     *
     * @param EntityInterface $item
     *
     * @return string
     */
    public function getItemEntityName(EntityInterface $item);

    /**
     * Persist the entity.
     *
     * @param EntityInterface $item
     *
     * @return self
     */
    public function persistItem(EntityInterface $item);

    /**
     * Flush changes.
     *
     * @return self
     */
    public function flush();
} 
