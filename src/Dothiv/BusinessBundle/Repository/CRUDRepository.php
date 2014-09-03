<?php


namespace Dothiv\BusinessBundle\Repository;

use PhpOption\Option;

interface CRUDRepository
{
    /**
     * Returns a list of items
     *
     * @param mixed|null $offsetKey
     * @param mixed|null $offsetDir
     *
     * @return PaginatedResult
     */
    public function getPaginated($offsetKey = null, $offsetDir = null);

    /**
     * Returns a single item
     *
     * @param string $identifier
     *
     * @return Option of Entity
     */
    public function getItemByIdentifier($identifier);
} 
