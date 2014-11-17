<?php

namespace Dothiv\BusinessBundle\Repository\CRUD;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use PhpOption\Option;

interface ReadEntityRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Returns a single item
     *
     * @param string $identifier
     *
     * @return Option of EntityInterface
     */
    public function getItemByIdentifier($identifier);
}
