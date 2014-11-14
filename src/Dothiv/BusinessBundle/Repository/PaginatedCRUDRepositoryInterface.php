<?php


namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\FilterQuery;
use PhpOption\Option;

interface PaginatedCRUDRepositoryInterface extends CRUDRepositoryInterface
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
} 
