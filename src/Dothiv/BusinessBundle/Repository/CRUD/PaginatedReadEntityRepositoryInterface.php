<?php


namespace Dothiv\BusinessBundle\Repository\CRUD;

use Dothiv\BusinessBundle\Model\FilterQuery;

interface PaginatedReadEntityRepositoryInterface extends ReadEntityRepositoryInterface
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
